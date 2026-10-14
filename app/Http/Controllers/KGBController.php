<?php

namespace App\Http\Controllers;

use App\Exports\KGBExport;
use App\Models\KGB;
use App\Models\Pegawai;
use Exception;
use Illuminate\Http\Request;
use App\Support\Access;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class KGBController extends Controller
{
    /**
     * Tampilkan daftar pegawai yang akan mendapat KGB berdasarkan periode.
     * Logika: KGB diberikan setiap 2 tahun sekali dihitung dari tmt_pns.
     * Periode "Tahun Ini"  => tahun saat ini
     * Periode "Tahun Depan" => tahun saat ini + 1
     */
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'tahun_ini');
        $tahunTarget = ($periode === 'tahun_depan')
            ? (int) date('Y') + 1
            : (int) date('Y');

        // Ambil pegawai yang tmt_pns-nya tidak null
        $query = Pegawai::whereNotNull('tmt_pns');

        if (Access::isAdmin()) {
            $query->where('unit_kerja_id', Access::unitKerjaId());
        } elseif (Access::isPegawai()) {
            $query->where('user_id', Access::user()->id);
        }

        $semuaPegawai = $query->get();

        // Filter pegawai yang jadwal KGB-nya jatuh pada tahun target
        // KGB diberikan setiap 2 tahun dari tmt_pns (bulan & tanggal sama, tahun kelipatan 2)
        $pegawaiKgb = $semuaPegawai->filter(function ($pegawai) use ($tahunTarget) {
            if (!$pegawai->tmt_pns) return false;

            $tmt = \Carbon\Carbon::parse($pegawai->tmt_pns);
            $selisihTahun = $tahunTarget - $tmt->year;

            // Jadwal KGB: setiap 2 tahun dari tmt_pns
            return $selisihTahun > 0 && $selisihTahun % 2 === 0;
        });

        return view('pages.dashboard.notifikasi_kgb.indexNotifikasiKgb', [
            'pegawaiKgb'  => $pegawaiKgb,
            'periode'     => $periode,
            'tahunTarget' => $tahunTarget,
        ]);
    }

    /**
     * Tampilkan form buat KGB untuk pegawai tertentu.
     */
    public function create(Request $request)
    {
        $pegawaiId = $request->input('pegawai_id');
        $pegawai   = Pegawai::findOrFail($pegawaiId);
        $this->authorizePegawaiTarget($pegawai);

        return view('pages.dashboard.notifikasi_kgb.buatKgb', [
            'pegawai' => $pegawai,
        ]);
    }

    /**
     * Admin may only act on pegawai inside their own unit kerja;
     * superadmin may act on any pegawai.
     */
    private function authorizePegawaiTarget(Pegawai $pegawai): void
    {
        if (Access::isSuperadmin()) {
            return;
        }

        if (Access::isAdmin()) {
            Access::ensureInUnit($pegawai);
            return;
        }

        abort(403, 'Akses ditolak');
    }

    /**
     * Simpan data KGB baru.
     */
    public function store(Request $request)
    {
        if (Access::isPegawai()) {
            abort(403, 'Akses ditolak');
        }

        $request->validate([
            'pegawai_id'            => 'required|exists:tb_pegawai,id',
            'no_kgb'                => 'nullable|string|max:255',
            'tgl_kgb'               => 'nullable|date',
            'pejabat'               => 'nullable|string|max:255',
            'no_sk_terakhir'        => 'nullable|string|max:255',
            'tgl_sk_terakhir'       => 'nullable|date',
            'tgl_berlaku_gaji'      => 'nullable|date',
            'masa_kerja_lama_tahun' => 'nullable|string|max:10',
            'masa_kerja_lama_bulan' => 'nullable|string|max:10',
            'gaji_baru'             => 'nullable|string|max:255',
            'gaji_baru_terbilang'   => 'nullable|string|max:500',
            'masa_kerja_baru_tahun' => 'nullable|string|max:10',
            'masa_kerja_baru_bulan' => 'nullable|string|max:10',
            'tmt_kgb'               => 'nullable|date',
            'tembusan.*'            => 'nullable|string|max:255',
            'periode'               => 'nullable|string|max:4',
        ]);

        $this->authorizePegawaiTarget(Pegawai::findOrFail($request->pegawai_id));

        try {
            DB::beginTransaction();

            // Bersihkan array tembusan dari nilai kosong
            $tembusan = array_values(array_filter($request->input('tembusan', []), fn($v) => !empty(trim($v))));

            KGB::create([
                'pegawai_id'            => $request->pegawai_id,
                'no_kgb'                => $request->no_kgb,
                'tgl_kgb'               => $request->tgl_kgb,
                'pejabat'               => $request->pejabat,
                'no_sk_terakhir'        => $request->no_sk_terakhir,
                'tgl_sk_terakhir'       => $request->tgl_sk_terakhir,
                'tgl_berlaku_gaji'      => $request->tgl_berlaku_gaji,
                'masa_kerja_lama_tahun' => $request->masa_kerja_lama_tahun,
                'masa_kerja_lama_bulan' => $request->masa_kerja_lama_bulan,
                'gaji_baru'             => $request->gaji_baru,
                'gaji_baru_terbilang'   => $request->gaji_baru_terbilang,
                'masa_kerja_baru_tahun' => $request->masa_kerja_baru_tahun,
                'masa_kerja_baru_bulan' => $request->masa_kerja_baru_bulan,
                'tmt_kgb'               => $request->tmt_kgb,
                'tembusan'              => $tembusan,
                'periode'               => $request->periode ?? date('Y'),
            ]);

            DB::commit();

            return redirect('/notifikasi_kgb/data_notifikasi_kgb')
                ->with('success', 'Data KGB berhasil disimpan!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan data KGB: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }

    public function exportExcelKGB(Request $request)
    {
        $periode     = $request->input('periode', 'tahun_ini');
        $tahunTarget = ($periode === 'tahun_depan')
            ? (int) date('Y') + 1
            : (int) date('Y');

        $label    = $periode === 'tahun_depan' ? 'TahunDepan' : 'TahunIni';
        $filename = "NotifikasiKGB_{$label}_{$tahunTarget}.xlsx";

        // Admin only sees their own unit; superadmin sees everything.
        $unitScope = Access::isAdmin() ? Access::unitKerjaId() : null;

        return Excel::download(new KGBExport($periode, $tahunTarget, $unitScope), $filename);
    }
}
