<?php

namespace App\Http\Controllers;

use App\Models\Tpp;
use App\Models\Pegawai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TppExport;

class TppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tpp = Tpp::with('pegawai')
                ->whereHas('pegawai', function($query) use ($user) {
                    $query->where('unit_kerja_id', $user->unit_kerja_id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            $tpp = $myPegawai
                ? Tpp::with('pegawai')->where('pegawai_id', $myPegawai->id)->orderBy('created_at', 'desc')->paginate(10)
                : collect()->paginate(10);
        } else {
            $tpp = Tpp::with('pegawai')->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('pages.dashboard.tpp.indexTpp', [
            'tpp' => $tpp,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->orderBy('nama')->get();
        } elseif ($user->role === 'pegawai') {
            $pegawai = Pegawai::where('user_id', $user->id)->orderBy('nama')->get();
        } else {
            $pegawai = Pegawai::orderBy('nama')->get();
        }

        return view('pages.dashboard.tpp.tambahTpp', [
            'pegawai' => $pegawai,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id'            => 'required|exists:tb_pegawai,id',
            'periode'               => 'required|string',
            'tahun'                 => 'required|string|size:4',
            'jml_hari_kerja'        => 'required|integer|min:0',
            'tidak_ada_produktifitas' => 'required|integer|min:0',
            'terlambat_1_30'        => 'required|integer|min:0',
            'terlambat_31_60'       => 'required|integer|min:0',
            'terlambat_61_90'       => 'required|integer|min:0',
            'terlambat_91_lebih'    => 'required|integer|min:0',
            'pulang_awal_1_30'      => 'required|integer|min:0',
            'pulang_awal_31_60'     => 'required|integer|min:0',
            'pulang_awal_61_90'     => 'required|integer|min:0',
            'pulang_awal_91_lebih'  => 'required|integer|min:0',
            'tidak_masuk_kerja'     => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::findOrFail($request->pegawai_id);
            $nilaiBasicTpp = $pegawai->nilai_tpp ?? 0;
            $jmlHariKerja  = $request->jml_hari_kerja > 0 ? $request->jml_hari_kerja : 1;

            // Pengurangan Produktifitas
            // Rumus: (tidak_ada_produktifitas / jml_hari_kerja) * nilai_basic_tpp * 0.6
            $penguranganProduktifitas = ($request->tidak_ada_produktifitas / $jmlHariKerja)
                * $nilaiBasicTpp * 0.6;

            // Pengurangan Disiplin (Keterlambatan + Pulang Awal + Mangkir)
            // Bobot per kategori (persentase per hari):
            // Terlambat 1-30 mnt  : 0.5%  | Terlambat 31-60 mnt : 1%
            // Terlambat 61-90 mnt : 1.25% | Terlambat >90 mnt   : 1.5%
            // Pulang awal 1-30    : 0.5%  | Pulang awal 31-60   : 1%
            // Pulang awal 61-90   : 1.25% | Pulang awal >90     : 1.5%
            // Tidak masuk kerja   : 3%
            $penguranganDisiplin =
                ($request->terlambat_1_30    * 0.005  * $nilaiBasicTpp) +
                ($request->terlambat_31_60   * 0.01   * $nilaiBasicTpp) +
                ($request->terlambat_61_90   * 0.0125 * $nilaiBasicTpp) +
                ($request->terlambat_91_lebih * 0.015 * $nilaiBasicTpp) +
                ($request->pulang_awal_1_30  * 0.005  * $nilaiBasicTpp) +
                ($request->pulang_awal_31_60 * 0.01   * $nilaiBasicTpp) +
                ($request->pulang_awal_61_90 * 0.0125 * $nilaiBasicTpp) +
                ($request->pulang_awal_91_lebih * 0.015 * $nilaiBasicTpp) +
                ($request->tidak_masuk_kerja * 0.03   * $nilaiBasicTpp);

            $tppDiterima = $nilaiBasicTpp - $penguranganProduktifitas - $penguranganDisiplin;
            $tppDiterima = max(0, $tppDiterima);

            Tpp::create([
                'pegawai_id'               => $request->pegawai_id,
                'periode'                  => $request->periode,
                'tahun'                    => $request->tahun,
                'jml_hari_kerja'           => $request->jml_hari_kerja,
                'tidak_ada_produktifitas'  => $request->tidak_ada_produktifitas,
                'terlambat_1_30'           => $request->terlambat_1_30,
                'terlambat_31_60'          => $request->terlambat_31_60,
                'terlambat_61_90'          => $request->terlambat_61_90,
                'terlambat_91_lebih'       => $request->terlambat_91_lebih,
                'pulang_awal_1_30'         => $request->pulang_awal_1_30,
                'pulang_awal_31_60'        => $request->pulang_awal_31_60,
                'pulang_awal_61_90'        => $request->pulang_awal_61_90,
                'pulang_awal_91_lebih'     => $request->pulang_awal_91_lebih,
                'tidak_masuk_kerja'        => $request->tidak_masuk_kerja,
                'nilai_basic_tpp'          => $nilaiBasicTpp,
                'pengurangan_produktifitas' => $penguranganProduktifitas,
                'pengurangan_disiplin'     => $penguranganDisiplin,
                'tpp_diterima'             => $tppDiterima,
            ]);

            DB::commit();

            return redirect('/tpp/input_tpp')->with('success', 'Data TPP berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan data TPP: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tpp $tpp)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $pegawai = Pegawai::where('unit_kerja_id', $user->unit_kerja_id)->orderBy('nama')->get();
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $tpp->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
            $pegawai = collect([$myPegawai]);
        } else {
            $pegawai = Pegawai::orderBy('nama')->get();
        }

        return view('pages.dashboard.tpp.editTpp', [
            'tpp'     => $tpp,
            'pegawai' => $pegawai,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tpp $tpp)
    {
        $request->validate([
            'pegawai_id'            => 'required|exists:tb_pegawai,id',
            'periode'               => 'required|string',
            'tahun'                 => 'required|string|size:4',
            'jml_hari_kerja'        => 'required|integer|min:0',
            'tidak_ada_produktifitas' => 'required|integer|min:0',
            'terlambat_1_30'        => 'required|integer|min:0',
            'terlambat_31_60'       => 'required|integer|min:0',
            'terlambat_61_90'       => 'required|integer|min:0',
            'terlambat_91_lebih'    => 'required|integer|min:0',
            'pulang_awal_1_30'      => 'required|integer|min:0',
            'pulang_awal_31_60'     => 'required|integer|min:0',
            'pulang_awal_61_90'     => 'required|integer|min:0',
            'pulang_awal_91_lebih'  => 'required|integer|min:0',
            'tidak_masuk_kerja'     => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::findOrFail($request->pegawai_id);
            $nilaiBasicTpp = $pegawai->nilai_tpp ?? 0;
            $jmlHariKerja  = $request->jml_hari_kerja > 0 ? $request->jml_hari_kerja : 1;

            $penguranganProduktifitas = ($request->tidak_ada_produktifitas / $jmlHariKerja)
                * $nilaiBasicTpp * 0.6;

            $penguranganDisiplin =
                ($request->terlambat_1_30    * 0.005  * $nilaiBasicTpp) +
                ($request->terlambat_31_60   * 0.01   * $nilaiBasicTpp) +
                ($request->terlambat_61_90   * 0.0125 * $nilaiBasicTpp) +
                ($request->terlambat_91_lebih * 0.015 * $nilaiBasicTpp) +
                ($request->pulang_awal_1_30  * 0.005  * $nilaiBasicTpp) +
                ($request->pulang_awal_31_60 * 0.01   * $nilaiBasicTpp) +
                ($request->pulang_awal_61_90 * 0.0125 * $nilaiBasicTpp) +
                ($request->pulang_awal_91_lebih * 0.015 * $nilaiBasicTpp) +
                ($request->tidak_masuk_kerja * 0.03   * $nilaiBasicTpp);

            $tppDiterima = max(0, $nilaiBasicTpp - $penguranganProduktifitas - $penguranganDisiplin);

            $tpp->update([
                'pegawai_id'               => $request->pegawai_id,
                'periode'                  => $request->periode,
                'tahun'                    => $request->tahun,
                'jml_hari_kerja'           => $request->jml_hari_kerja,
                'tidak_ada_produktifitas'  => $request->tidak_ada_produktifitas,
                'terlambat_1_30'           => $request->terlambat_1_30,
                'terlambat_31_60'          => $request->terlambat_31_60,
                'terlambat_61_90'          => $request->terlambat_61_90,
                'terlambat_91_lebih'       => $request->terlambat_91_lebih,
                'pulang_awal_1_30'         => $request->pulang_awal_1_30,
                'pulang_awal_31_60'        => $request->pulang_awal_31_60,
                'pulang_awal_61_90'        => $request->pulang_awal_61_90,
                'pulang_awal_91_lebih'     => $request->pulang_awal_91_lebih,
                'tidak_masuk_kerja'        => $request->tidak_masuk_kerja,
                'nilai_basic_tpp'          => $nilaiBasicTpp,
                'pengurangan_produktifitas' => $penguranganProduktifitas,
                'pengurangan_disiplin'     => $penguranganDisiplin,
                'tpp_diterima'             => $tppDiterima,
            ]);

            DB::commit();

            return redirect('/tpp/input_tpp')->with('success', 'Data TPP berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui data TPP: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tpp $tpp)
    {
        // Pastikan pegawai role hanya bisa hapus data miliknya sendiri
        $user = Auth::user();
        if ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if (!$myPegawai || $tpp->pegawai_id != $myPegawai->id) {
                abort(403, 'Akses ditolak');
            }
        }

        try {
            $tpp->delete();
            return redirect('/tpp/input_tpp')->with('success', 'Data TPP berhasil dihapus!');
        } catch (Exception $e) {
            Log::error('Gagal menghapus data TPP: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }

    /**
     * Laporan bulanan TPP berdasarkan periode & tahun.
     */
    public function laporanBulanan(Request $request)
    {
        $bulanList = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember',
        ];

        $periode = $request->input('periode');
        $tahun   = $request->input('tahun');

        $data = collect();

        if ($periode && $tahun) {
            $data = Tpp::with(['pegawai.jabatan.master_jabatan'])
                ->where('periode', $periode)
                ->where('tahun', $tahun)
                ->orderBy('id')
                ->get();
        }

        return view('pages.dashboard.tpp.laporanBulananTpp', [
            'bulanList'     => $bulanList,
            'selectedBulan' => $periode,
            'selectedTahun' => $tahun,
            'data'          => $data,
        ]);
    }

    public function cariTpp(Request $request)
    {
        $user = Auth::user();

        $query = Tpp::with('pegawai');

        // Filter berdasarkan role
        if ($user->role === 'admin') {
            $query->whereHas('pegawai', function($q) use ($user) {
                $q->where('unit_kerja_id', $user->unit_kerja_id);
            });
        } elseif ($user->role === 'pegawai') {
            $myPegawai = Pegawai::where('user_id', $user->id)->first();
            if ($myPegawai) {
                $query->where('pegawai_id', $myPegawai->id);
            } else {
                $query->whereRaw('1=0');
            }
        }

        if ($request->cariTpp) {
            $query->where(function($q) use ($request) {
                $q->where('periode', 'like', '%' . $request->cariTpp . '%')
                  ->orWhere('tahun', 'like', '%' . $request->cariTpp . '%')
                  ->orWhereHas('pegawai', function($q2) use ($request) {
                      $q2->where('nama', 'like', '%' . $request->cariTpp . '%');
                  });
            });
        }

        $tpp = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pages.dashboard.tpp.indexTpp', [
            'tpp' => $tpp,
        ]);
    }

    public function exportExcelLaporanTpp(Request $request)
    {
        $periode  = $request->input('periode');
        $tahun    = $request->input('tahun');
        $filename = 'LaporanTPP' . ($periode ? "_$periode" : '') . ($tahun ? "_$tahun" : '') . '.xlsx';

        return Excel::download(new TppExport($periode, $tahun), $filename);
    }
}
