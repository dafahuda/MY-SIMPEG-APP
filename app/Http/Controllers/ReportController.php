<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitKerja;
use App\Models\Pegawai;
use App\Models\Pangkat;
use App\Models\InstansiLembaga;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\RiwayatPendidikanLanjut;

class ReportController extends Controller
{
    public function reportNominatif(Request $request)
    {
        $unitKerjaList = UnitKerja::orderBy('nama_unit')->get();
        $instansi      = InstansiLembaga::first();

        $unitKerja  = null;
        $pegawaiList = collect();

        if ($request->filled('unit_kerja_id')) {
            $unitKerja = UnitKerja::find($request->unit_kerja_id);

            $pegawaiList = Pegawai::with([
                    'jabatan_aktif.master_jabatan',
                    'jabatan_aktif.master_eselon',
                ])
                ->where('unit_kerja_id', $request->unit_kerja_id)
                ->orderBy('nama')
                ->get()
                ->map(function ($pegawai) {
                    // Pangkat terakhir
                    $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
                        ->where('pegawai_id', $pegawai->id)
                        ->latest('tmt_pangkat_mulai')
                        ->first();

                    // Pendidikan terakhir (gabung sekolah + lanjut, ambil tertinggi)
                    $jenjangOrder = ['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'];

                    $pendSekolah = RiwayatPendidikanSekolah::where('pegawai_id', $pegawai->id)
                        ->latest('tgl_ijazah')->first();
                    $pendLanjut  = RiwayatPendidikanLanjut::where('pegawai_id', $pegawai->id)
                        ->latest('thn_selesai')->first();

                    // Pilih yang jenjangnya lebih tinggi
                    $pendidikan = null;
                    $idxSekolah = $pendSekolah
                        ? (array_search(strtoupper(trim($pendSekolah->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                        : -1;
                    $idxLanjut  = $pendLanjut
                        ? (array_search(strtoupper(trim($pendLanjut->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                        : -1;

                    if ($idxLanjut >= $idxSekolah && $pendLanjut) {
                        $pendidikan = $pendLanjut;
                    } elseif ($pendSekolah) {
                        $pendidikan = $pendSekolah;
                    }

                    $pegawai->pangkat_terakhir = $pangkat;
                    $pegawai->pendidikan_terakhir = $pendidikan;

                    return $pegawai;
                });
        }

        return view('pages.dashboard.report.nominatif', [
            'unitKerjaList' => $unitKerjaList,
            'selectedUnitKerja' => $unitKerja,
            'pegawaiList'   => $pegawaiList,
            'instansi'      => $instansi,
        ]);
    }

    public function printNominatif(Request $request)
    {
        $instansi  = InstansiLembaga::first();
        $unitKerja = UnitKerja::find($request->unit_kerja_id);

        if (!$unitKerja) {
            return redirect()->route('report.nominatif')->with('error', 'Unit kerja tidak ditemukan.');
        }

        $jenjangOrder = ['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'];

        $pegawaiList = Pegawai::with([
                'jabatan_aktif.master_jabatan',
                'jabatan_aktif.master_eselon',
            ])
            ->where('unit_kerja_id', $request->unit_kerja_id)
            ->orderBy('nama')
            ->get()
            ->map(function ($pegawai) use ($jenjangOrder) {
                $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
                    ->where('pegawai_id', $pegawai->id)
                    ->latest('tmt_pangkat_mulai')
                    ->first();

                $pendSekolah = RiwayatPendidikanSekolah::where('pegawai_id', $pegawai->id)
                    ->latest('tgl_ijazah')->first();
                $pendLanjut  = RiwayatPendidikanLanjut::where('pegawai_id', $pegawai->id)
                    ->latest('thn_selesai')->first();

                $idxSekolah = $pendSekolah
                    ? (array_search(strtoupper(trim($pendSekolah->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                    : -1;
                $idxLanjut  = $pendLanjut
                    ? (array_search(strtoupper(trim($pendLanjut->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                    : -1;

                $pendidikan = ($idxLanjut >= $idxSekolah && $pendLanjut) ? $pendLanjut : $pendSekolah;

                $pegawai->pangkat_terakhir    = $pangkat;
                $pegawai->pendidikan_terakhir = $pendidikan;

                return $pegawai;
            });

        return view('pages.dashboard.report.nominatif_print', [
            'unitKerja'   => $unitKerja,
            'pegawaiList' => $pegawaiList,
            'instansi'    => $instansi,
        ]);
    }

    public function reportDUK(Request $request)
    {
        $unitKerjaList = UnitKerja::orderBy('nama_unit')->get();
        $instansi      = InstansiLembaga::first();

        $unitKerja   = null;
        $pegawaiList = collect();

        if ($request->filled('unit_kerja_id')) {
            $unitKerja = UnitKerja::find($request->unit_kerja_id);

            $pegawaiList = $this->buildDukData($request->unit_kerja_id);
        }

        return view('pages.dashboard.report.duk', [
            'unitKerjaList'     => $unitKerjaList,
            'selectedUnitKerja' => $unitKerja,
            'pegawaiList'       => $pegawaiList,
            'instansi'          => $instansi,
            'tahun'             => now()->year,
        ]);
    }

    public function printDUK(Request $request)
    {
        $instansi  = InstansiLembaga::first();
        $unitKerja = UnitKerja::find($request->unit_kerja_id);

        if (!$unitKerja) {
            return redirect()->route('report.duk')->with('error', 'Unit kerja tidak ditemukan.');
        }

        $pegawaiList = $this->buildDukData($request->unit_kerja_id);

        return view('pages.dashboard.report.duk_print', [
            'unitKerja'   => $unitKerja,
            'pegawaiList' => $pegawaiList,
            'instansi'    => $instansi,
            'tahun'       => now()->year,
        ]);
    }

    /**
     * Build DUK pegawai data for a given unit kerja.
     */
    private function buildDukData(int $unitKerjaId): \Illuminate\Support\Collection
    {
        $jenjangOrder = ['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'];

        return Pegawai::with([
                'jabatan_aktif.master_jabatan',
                'jabatan_aktif.master_eselon',
            ])
            ->where('unit_kerja_id', $unitKerjaId)
            ->orderBy('nama')
            ->get()
            ->map(function ($pegawai) use ($jenjangOrder) {
                // Pangkat terakhir
                $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
                    ->where('pegawai_id', $pegawai->id)
                    ->latest('tmt_pangkat_mulai')
                    ->first();

                // Masa Kerja Golongan: dari tmt_pangkat_mulai sampai sekarang
                $mkThn = 0;
                $mkBln = 0;
                if ($pangkat && $pangkat->tmt_pangkat_mulai) {
                    $tmt  = \Carbon\Carbon::parse($pangkat->tmt_pangkat_mulai);
                    $now  = \Carbon\Carbon::now();
                    $diff = $tmt->diff($now);
                    $mkThn = $diff->y;
                    $mkBln = $diff->m;
                }

                // Pendidikan tertinggi
                $pendSekolah = RiwayatPendidikanSekolah::where('pegawai_id', $pegawai->id)
                    ->latest('tgl_ijazah')->first();
                $pendLanjut  = RiwayatPendidikanLanjut::where('pegawai_id', $pegawai->id)
                    ->latest('thn_selesai')->first();

                $idxSekolah = $pendSekolah
                    ? (array_search(strtoupper(trim($pendSekolah->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                    : -1;
                $idxLanjut  = $pendLanjut
                    ? (array_search(strtoupper(trim($pendLanjut->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                    : -1;

                $pendidikan = ($idxLanjut >= $idxSekolah && $pendLanjut) ? $pendLanjut : $pendSekolah;

                $pegawai->pangkat_terakhir    = $pangkat;
                $pegawai->mk_thn              = $mkThn;
                $pegawai->mk_bln              = $mkBln;
                $pegawai->pendidikan_terakhir = $pendidikan;

                return $pegawai;
            });
    }

    public function reportKeadaanPegawai(Request $request)
    {
        $unitKerjaList = UnitKerja::orderBy('nama_unit')->get();
        $instansi      = InstansiLembaga::first();

        $unitKerja = null;
        $stats     = null;

        if ($request->filled('unit_kerja_id')) {
            $unitKerja = UnitKerja::find($request->unit_kerja_id);
            $stats     = $this->buildKeadaanData($request->unit_kerja_id);
        }

        return view('pages.dashboard.report.keadaan_pegawai', [
            'unitKerjaList'     => $unitKerjaList,
            'selectedUnitKerja' => $unitKerja,
            'stats'             => $stats,
            'instansi'          => $instansi,
            'bulan'             => now()->month,
            'tahun'             => now()->year,
        ]);
    }

    public function printKeadaanPegawai(Request $request)
    {
        $instansi  = InstansiLembaga::first();
        $unitKerja = UnitKerja::find($request->unit_kerja_id);

        if (!$unitKerja) {
            return redirect()->route('report.keadaan_pegawai')->with('error', 'Unit kerja tidak ditemukan.');
        }

        $stats = $this->buildKeadaanData($request->unit_kerja_id);

        return view('pages.dashboard.report.keadaan_pegawai_print', [
            'unitKerja' => $unitKerja,
            'stats'     => $stats,
            'instansi'  => $instansi,
            'bulan'     => now()->month,
            'tahun'     => now()->year,
        ]);
    }

    /**
     * Build keadaan pegawai statistics for a given unit kerja.
     * Returns a structured array matching the report layout.
     */
    private function buildKeadaanData(int $unitKerjaId): array
    {
        $jenjangOrder = ['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'];

        // Semua pegawai di unit kerja ini
        $pegawaiAll = Pegawai::with(['jabatan_aktif.master_eselon'])
            ->where('unit_kerja_id', $unitKerjaId)
            ->get();

        $pegawaiIds = $pegawaiAll->pluck('id')->toArray();

        // Pangkat terakhir per pegawai
        $pangkatMap = \App\Models\Pangkat::with('master_golongan')
            ->whereIn('pegawai_id', $pegawaiIds)
            ->get()
            ->groupBy('pegawai_id')
            ->map(fn($rows) => $rows->sortByDesc('tmt_pangkat_mulai')->first());

        // Pendidikan tertinggi per pegawai
        $pendSekolahMap = \App\Models\RiwayatPendidikanSekolah::whereIn('pegawai_id', $pegawaiIds)
            ->get()->groupBy('pegawai_id')
            ->map(fn($rows) => $rows->sortByDesc('tgl_ijazah')->first());
        $pendLanjutMap  = \App\Models\RiwayatPendidikanLanjut::whereIn('pegawai_id', $pegawaiIds)
            ->get()->groupBy('pegawai_id')
            ->map(fn($rows) => $rows->sortByDesc('thn_selesai')->first());

        // Mutasi bulan berjalan untuk unit kerja ini
        $bulanAwal = now()->startOfMonth();
        $bulanAkhir = now()->endOfMonth();
        $mutasiMasuk  = \App\Models\Mutasi::whereIn('pegawai_id', $pegawaiIds)
            ->where('jenis_mutasi', 'masuk')
            ->whereBetween('tgl_sk_mutasi', [$bulanAwal, $bulanAkhir])
            ->count();
        $mutasiKeluar = \App\Models\Mutasi::whereIn('pegawai_id', $pegawaiIds)
            ->where('jenis_mutasi', 'keluar')
            ->whereBetween('tgl_sk_mutasi', [$bulanAwal, $bulanAkhir])
            ->count();

        // Golongan yang dipakai sebagai kolom (I, II, III, IV)
        $golKelompok = ['I' => [], 'II' => [], 'III' => [], 'IV' => []];

        // Helper: tentukan kelompok golongan dari nama_golongan (I/A, II/B, III/C, IV/D, dst)
        $getKelompokGol = function (?string $namaGol): ?string {
            if (!$namaGol) return null;
            if (str_starts_with($namaGol, 'I/') || $namaGol === 'I')   return 'I';
            if (str_starts_with($namaGol, 'II/') || $namaGol === 'II') return 'II';
            if (str_starts_with($namaGol, 'III/'))                      return 'III';
            if (str_starts_with($namaGol, 'IV/'))                       return 'IV';
            return null;
        };

        // Helper: tentukan kelompok eselon (II, III, IV, V, Staff)
        $getKelompokEsl = function (?string $namaEsl): string {
            if (!$namaEsl) return 'staff';
            $n = strtoupper(trim($namaEsl));
            if (str_starts_with($n, 'II'))  return 'II';
            if (str_starts_with($n, 'III')) return 'III';
            if (str_starts_with($n, 'IV'))  return 'IV';
            if (str_starts_with($n, 'V'))   return 'V';
            return 'staff';
        };

        // Inisialisasi counter
        $zero = ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, 'esl_II' => 0, 'esl_III' => 0, 'esl_IV' => 0, 'esl_V' => 0, 'staff' => 0];

        $cnt = [
            'laki'      => $zero,
            'perempuan' => $zero,
            'sd'        => $zero,
            'smp'       => $zero,
            'sma'       => $zero,
            'd2d3'      => $zero,
            'd4s1'      => $zero,
            's2s3'      => $zero,
            'profesi'   => $zero,
        ];

        foreach ($pegawaiAll as $pegawai) {
            $pangkat   = $pangkatMap[$pegawai->id] ?? null;
            $namaGol   = $pangkat?->master_golongan?->nama_golongan;
            $kGol      = $getKelompokGol($namaGol);

            $jabatan   = $pegawai->jabatan_aktif;
            $namaEsl   = $jabatan?->master_eselon?->nama_eselon;
            $kEsl      = $getKelompokEsl($namaEsl);

            // Pendidikan tertinggi
            $ps  = $pendSekolahMap[$pegawai->id] ?? null;
            $pl  = $pendLanjutMap[$pegawai->id] ?? null;
            $iS  = $ps ? (array_search(strtoupper(trim($ps->jenjang_pendidikan)), $jenjangOrder) ?: -1) : -1;
            $iL  = $pl ? (array_search(strtoupper(trim($pl->jenjang_pendidikan)), $jenjangOrder) ?: -1) : -1;
            $pend = ($iL >= $iS && $pl) ? $pl : $ps;
            $jenjang = $pend ? strtoupper(trim($pend->jenjang_pendidikan)) : null;

            // Fungsi increment
            $inc = function (string $key) use (&$cnt, $kGol, $kEsl) {
                if ($kGol) $cnt[$key][$kGol]++;
                $cnt[$key]['esl_' . $kEsl] = ($cnt[$key]['esl_' . $kEsl] ?? 0) + 1;
                if ($kEsl === 'staff') $cnt[$key]['staff']++;
            };

            // Jenis kelamin
            if (strtolower($pegawai->jenis_kelamin) === 'laki-laki') {
                $inc('laki');
            } else {
                $inc('perempuan');
            }

            // Pendidikan
            if (in_array($jenjang, ['SD'])) {
                $inc('sd');
            } elseif (in_array($jenjang, ['SMP'])) {
                $inc('smp');
            } elseif (in_array($jenjang, ['SMA','SMK'])) {
                $inc('sma');
            } elseif (in_array($jenjang, ['D2','D3'])) {
                $inc('d2d3');
            } elseif (in_array($jenjang, ['D4','S1'])) {
                $inc('d4s1');
            } elseif (in_array($jenjang, ['S2','S3'])) {
                $inc('s2s3');
            } elseif (in_array($jenjang, ['PROFESI'])) {
                $inc('profesi');
            }
        }

        // Hitung jumlah total per baris (gol I+II+III+IV)
        $sumGol = fn(array $row) => $row['I'] + $row['II'] + $row['III'] + $row['IV'];

        return [
            'cnt'           => $cnt,
            'sumGol'        => $sumGol,
            'mutasi_masuk'  => $mutasiMasuk,
            'mutasi_keluar' => $mutasiKeluar,
            'total'         => $pegawaiAll->count(),
        ];
    }

    public function reportBezetting(Request $request)
    {
        $unitKerjaList = UnitKerja::orderBy('nama_unit')->get();
        $instansi      = InstansiLembaga::first();

        $unitKerja   = null;
        $pegawaiList = collect();

        if ($request->filled('unit_kerja_id')) {
            $unitKerja = UnitKerja::find($request->unit_kerja_id);

            $jenjangOrder = ['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'];

            $pegawaiList = Pegawai::with([
                    'jabatan_aktif.master_jabatan',
                ])
                ->where('unit_kerja_id', $request->unit_kerja_id)
                ->orderBy('nama')
                ->get()
                ->map(function ($pegawai) use ($jenjangOrder) {
                    // Pangkat terakhir
                    $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
                        ->where('pegawai_id', $pegawai->id)
                        ->latest('tmt_pangkat_mulai')
                        ->first();

                    // Pendidikan tertinggi
                    $pendSekolah = RiwayatPendidikanSekolah::where('pegawai_id', $pegawai->id)
                        ->latest('tgl_ijazah')->first();
                    $pendLanjut  = RiwayatPendidikanLanjut::where('pegawai_id', $pegawai->id)
                        ->latest('thn_selesai')->first();

                    $idxSekolah = $pendSekolah
                        ? (array_search(strtoupper(trim($pendSekolah->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                        : -1;
                    $idxLanjut  = $pendLanjut
                        ? (array_search(strtoupper(trim($pendLanjut->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                        : -1;

                    $pendidikan = ($idxLanjut >= $idxSekolah && $pendLanjut) ? $pendLanjut : $pendSekolah;

                    // Usia dalam tahun
                    $usia = $pegawai->tgl_lahir
                        ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->age
                        : '-';

                    $pegawai->pangkat_terakhir    = $pangkat;
                    $pegawai->pendidikan_terakhir = $pendidikan;
                    $pegawai->usia                = $usia;

                    return $pegawai;
                });
        }

        return view('pages.dashboard.report.bezetting', [
            'unitKerjaList'     => $unitKerjaList,
            'selectedUnitKerja' => $unitKerja,
            'pegawaiList'       => $pegawaiList,
            'instansi'          => $instansi,
            'bulan'             => now()->month,
            'tahun'             => now()->year,
        ]);
    }

    public function printBezetting(Request $request)
    {
        $instansi  = InstansiLembaga::first();
        $unitKerja = UnitKerja::find($request->unit_kerja_id);

        if (!$unitKerja) {
            return redirect()->route('report.bezetting')->with('error', 'Unit kerja tidak ditemukan.');
        }

        $jenjangOrder = ['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'];

        $pegawaiList = Pegawai::with(['jabatan_aktif.master_jabatan'])
            ->where('unit_kerja_id', $request->unit_kerja_id)
            ->orderBy('nama')
            ->get()
            ->map(function ($pegawai) use ($jenjangOrder) {
                $pangkat = Pangkat::with(['master_pangkat', 'master_golongan'])
                    ->where('pegawai_id', $pegawai->id)
                    ->latest('tmt_pangkat_mulai')
                    ->first();

                $pendSekolah = RiwayatPendidikanSekolah::where('pegawai_id', $pegawai->id)
                    ->latest('tgl_ijazah')->first();
                $pendLanjut  = RiwayatPendidikanLanjut::where('pegawai_id', $pegawai->id)
                    ->latest('thn_selesai')->first();

                $idxSekolah = $pendSekolah
                    ? (array_search(strtoupper(trim($pendSekolah->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                    : -1;
                $idxLanjut  = $pendLanjut
                    ? (array_search(strtoupper(trim($pendLanjut->jenjang_pendidikan)), $jenjangOrder) ?: -1)
                    : -1;

                $pendidikan = ($idxLanjut >= $idxSekolah && $pendLanjut) ? $pendLanjut : $pendSekolah;

                $pegawai->pangkat_terakhir    = $pangkat;
                $pegawai->pendidikan_terakhir = $pendidikan;
                $pegawai->usia                = $pegawai->tgl_lahir
                    ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->age
                    : '-';

                return $pegawai;
            });

        return view('pages.dashboard.report.bezetting_print', [
            'unitKerja'   => $unitKerja,
            'pegawaiList' => $pegawaiList,
            'instansi'    => $instansi,
            'bulan'       => now()->month,
            'tahun'       => now()->year,
        ]);
    }

    public function reportPensiun(Request $request)
    {
        // Opsi periode: tahun ini, 1 tahun akan datang, 2 tahun akan datang
        $periodeOptions = [
            'tahun_ini'  => 'Tahun Ini',
            '1_tahun'    => '1 Tahun Yang Akan Datang',
            '2_tahun'    => '2 Tahun Yang Akan Datang',
        ];

        $selectedPeriode = $request->input('periode', '');
        $pegawaiList     = collect();

        if ($selectedPeriode) {
            $tahunSekarang = now()->year;

            // Usia pensiun PNS = 58 tahun (umum) / 60 tahun (pejabat pimpinan tinggi)
            // Kita pakai 58 sebagai default
            $batasUsiaPensiun = 58;

            // Tentukan rentang tahun lahir yang akan pensiun
            switch ($selectedPeriode) {
                case 'tahun_ini':
                    $tahunLahirMin = $tahunSekarang - $batasUsiaPensiun;
                    $tahunLahirMax = $tahunSekarang - $batasUsiaPensiun;
                    break;
                case '1_tahun':
                    $tahunLahirMin = ($tahunSekarang + 1) - $batasUsiaPensiun;
                    $tahunLahirMax = ($tahunSekarang + 1) - $batasUsiaPensiun;
                    break;
                case '2_tahun':
                    $tahunLahirMin = ($tahunSekarang + 2) - $batasUsiaPensiun;
                    $tahunLahirMax = ($tahunSekarang + 2) - $batasUsiaPensiun;
                    break;
                default:
                    $tahunLahirMin = $tahunSekarang - $batasUsiaPensiun;
                    $tahunLahirMax = $tahunSekarang - $batasUsiaPensiun;
            }

            $pegawaiList = Pegawai::with(['jabatan_aktif.master_jabatan', 'unit_kerja'])
                ->whereYear('tgl_lahir', '>=', $tahunLahirMin)
                ->whereYear('tgl_lahir', '<=', $tahunLahirMax)
                ->orderBy('tgl_lahir')
                ->get()
                ->map(function ($pegawai) use ($batasUsiaPensiun) {
                    // Tanggal pensiun = ulang tahun ke-58 (atau sesuai batas)
                    $tglLahir    = \Carbon\Carbon::parse($pegawai->tgl_lahir);
                    $tglPensiun  = $tglLahir->copy()->addYears($batasUsiaPensiun);

                    $pegawai->tgl_pensiun    = $tglPensiun->format('Y-m-d');
                    $pegawai->periode_pensiun = $tglPensiun->translatedFormat('F Y');

                    return $pegawai;
                });
        }

        return view('pages.dashboard.report.pensiun', [
            'periodeOptions'  => $periodeOptions,
            'selectedPeriode' => $selectedPeriode,
            'pegawaiList'     => $pegawaiList,
        ]);
    }
}
