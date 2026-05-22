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
    private const JENJANG_ORDER = ['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3'];

    private function isAdminScoped(Request $request): bool
    {
        return $request->user()?->role === 'admin';
    }

    private function scopedUnitKerjaList(Request $request)
    {
        $query = UnitKerja::orderBy('nama_unit');

        if ($this->isAdminScoped($request)) {
            $query->where('id', $request->user()->unit_kerja_id);
        }

        return $query->get();
    }

    private function selectedUnitKerjaId(Request $request): ?int
    {
        if ($this->isAdminScoped($request)) {
            return $request->user()->unit_kerja_id;
        }

        return $request->filled('unit_kerja_id') ? (int) $request->unit_kerja_id : null;
    }

    private function authorizePrintableUnit(Request $request): UnitKerja
    {
        $unitKerjaId = $this->selectedUnitKerjaId($request);

        abort_if(!$unitKerjaId, 404);

        $unitKerja = UnitKerja::find($unitKerjaId);

        abort_if(!$unitKerja, 404);

        return $unitKerja;
    }

    private function educationRankIndex(?string $jenjang): int
    {
        if ($jenjang === null) {
            return -1;
        }

        $index = array_search(strtoupper(trim($jenjang)), self::JENJANG_ORDER, true);

        return $index === false ? -1 : $index;
    }

    private function latestPangkatMap(array $pegawaiIds)
    {
        return Pangkat::with(['master_pangkat', 'master_golongan'])
            ->whereIn('pegawai_id', $pegawaiIds)
            ->get()
            ->groupBy('pegawai_id')
            ->map(fn ($rows) => $rows->sortByDesc('tmt_pangkat_mulai')->first());
    }

    private function latestEducationMaps(array $pegawaiIds): array
    {
        return [
            RiwayatPendidikanSekolah::whereIn('pegawai_id', $pegawaiIds)
                ->get()
                ->groupBy('pegawai_id')
                ->map(fn ($rows) => $rows->sortByDesc('tgl_ijazah')->first()),
            RiwayatPendidikanLanjut::whereIn('pegawai_id', $pegawaiIds)
                ->get()
                ->groupBy('pegawai_id')
                ->map(fn ($rows) => $rows->sortByDesc('thn_selesai')->first()),
        ];
    }

    private function highestEducation($pendSekolah, $pendLanjut)
    {
        $idxSekolah = $pendSekolah ? $this->educationRankIndex($pendSekolah->jenjang_pendidikan) : -1;
        $idxLanjut = $pendLanjut ? $this->educationRankIndex($pendLanjut->jenjang_pendidikan) : -1;

        return ($idxLanjut >= $idxSekolah && $pendLanjut) ? $pendLanjut : $pendSekolah;
    }

    private function enrichPegawaiForReport($pegawaiList, bool $includeUsia = false, bool $includeMasaKerja = false)
    {
        $pegawaiIds = $pegawaiList->pluck('id')->all();
        $pangkatMap = $this->latestPangkatMap($pegawaiIds);
        [$pendSekolahMap, $pendLanjutMap] = $this->latestEducationMaps($pegawaiIds);

        return $pegawaiList->map(function ($pegawai) use ($pangkatMap, $pendSekolahMap, $pendLanjutMap, $includeUsia, $includeMasaKerja) {
            $pangkat = $pangkatMap[$pegawai->id] ?? null;

            $pegawai->pangkat_terakhir = $pangkat;
            $pegawai->pendidikan_terakhir = $this->highestEducation(
                $pendSekolahMap[$pegawai->id] ?? null,
                $pendLanjutMap[$pegawai->id] ?? null
            );

            if ($includeMasaKerja) {
                $pegawai->mk_thn = 0;
                $pegawai->mk_bln = 0;

                if ($pangkat && $pangkat->tmt_pangkat_mulai) {
                    $diff = \Carbon\Carbon::parse($pangkat->tmt_pangkat_mulai)->diff(\Carbon\Carbon::now());
                    $pegawai->mk_thn = $diff->y;
                    $pegawai->mk_bln = $diff->m;
                }
            }

            if ($includeUsia) {
                $pegawai->usia = $pegawai->tgl_lahir
                    ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->age
                    : '-';
            }

            return $pegawai;
        });
    }

    private function normalizedGender(?string $gender): ?string
    {
        $normalized = $gender === null ? null : strtolower(trim($gender));

        return in_array($normalized, ['laki-laki', 'perempuan'], true) ? $normalized : null;
    }

    public function reportNominatif(Request $request)
    {
        $unitKerjaList = $this->scopedUnitKerjaList($request);
        $instansi      = InstansiLembaga::first();

        $unitKerja  = null;
        $pegawaiList = collect();

        $unitKerjaId = $this->selectedUnitKerjaId($request);

        if ($unitKerjaId) {
            $unitKerja = UnitKerja::find($unitKerjaId);

            $pegawaiList = $this->enrichPegawaiForReport(
                Pegawai::with([
                        'jabatan_aktif.master_jabatan',
                        'jabatan_aktif.master_eselon',
                    ])
                    ->where('unit_kerja_id', $unitKerjaId)
                    ->orderBy('nama')
                    ->get()
            );
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
        $unitKerja = $this->authorizePrintableUnit($request);

        $pegawaiList = $this->enrichPegawaiForReport(
            Pegawai::with([
                    'jabatan_aktif.master_jabatan',
                    'jabatan_aktif.master_eselon',
                ])
                ->where('unit_kerja_id', $unitKerja->id)
                ->orderBy('nama')
                ->get()
        );

        return view('pages.dashboard.report.nominatif_print', [
            'unitKerja'   => $unitKerja,
            'pegawaiList' => $pegawaiList,
            'instansi'    => $instansi,
        ]);
    }

    public function reportDUK(Request $request)
    {
        $unitKerjaList = $this->scopedUnitKerjaList($request);
        $instansi      = InstansiLembaga::first();

        $unitKerja   = null;
        $pegawaiList = collect();
        $unitKerjaId = $this->selectedUnitKerjaId($request);

        if ($unitKerjaId) {
            $unitKerja = UnitKerja::find($unitKerjaId);

            $pegawaiList = $this->buildDukData($unitKerjaId);
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
        $unitKerja = $this->authorizePrintableUnit($request);

        $pegawaiList = $this->buildDukData($unitKerja->id);

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
        return $this->enrichPegawaiForReport(
            Pegawai::with([
                    'jabatan_aktif.master_jabatan',
                    'jabatan_aktif.master_eselon',
                ])
                ->where('unit_kerja_id', $unitKerjaId)
                ->orderBy('nama')
                ->get(),
            includeMasaKerja: true
        );
    }

    public function reportKeadaanPegawai(Request $request)
    {
        $unitKerjaList = $this->scopedUnitKerjaList($request);
        $instansi      = InstansiLembaga::first();

        $unitKerja   = null;
        $stats       = null;
        $unitKerjaId = $this->selectedUnitKerjaId($request);

        if ($unitKerjaId) {
            $unitKerja = UnitKerja::find($unitKerjaId);
            $stats     = $this->buildKeadaanData($unitKerjaId);
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
        $unitKerja = $this->authorizePrintableUnit($request);

        $stats = $this->buildKeadaanData($unitKerja->id);

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
        // Semua pegawai di unit kerja ini
        $pegawaiAll = Pegawai::with(['jabatan_aktif.master_eselon'])
            ->where('unit_kerja_id', $unitKerjaId)
            ->get();

        $pegawaiIds = $pegawaiAll->pluck('id')->toArray();

        // Pangkat terakhir per pegawai
        $pangkatMap = $this->latestPangkatMap($pegawaiIds);

        // Pendidikan tertinggi per pegawai
        [$pendSekolahMap, $pendLanjutMap] = $this->latestEducationMaps($pegawaiIds);

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
            $pend = $this->highestEducation($ps, $pl);
            $jenjang = $pend ? strtoupper(trim($pend->jenjang_pendidikan)) : null;

            // Fungsi increment
            $inc = function (string $key) use (&$cnt, $kGol, $kEsl) {
                if ($kGol) $cnt[$key][$kGol]++;
                $cnt[$key]['esl_' . $kEsl] = ($cnt[$key]['esl_' . $kEsl] ?? 0) + 1;
                if ($kEsl === 'staff') $cnt[$key]['staff']++;
            };

            // Jenis kelamin: nilai null/unknown tidak dimasukkan ke bucket laki/perempuan.
            $gender = $this->normalizedGender($pegawai->jenis_kelamin);
            if ($gender === 'laki-laki') {
                $inc('laki');
            } elseif ($gender === 'perempuan') {
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
        $unitKerjaList = $this->scopedUnitKerjaList($request);
        $instansi      = InstansiLembaga::first();

        $unitKerja   = null;
        $pegawaiList = collect();
        $unitKerjaId = $this->selectedUnitKerjaId($request);

        if ($unitKerjaId) {
            $unitKerja = UnitKerja::find($unitKerjaId);

            $pegawaiList = $this->enrichPegawaiForReport(
                Pegawai::with([
                        'jabatan_aktif.master_jabatan',
                    ])
                    ->where('unit_kerja_id', $unitKerja->id)
                    ->orderBy('nama')
                    ->get(),
                includeUsia: true
            );
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
        $unitKerja = $this->authorizePrintableUnit($request);

        $pegawaiList = $this->enrichPegawaiForReport(
            Pegawai::with(['jabatan_aktif.master_jabatan'])
                ->where('unit_kerja_id', $unitKerja->id)
                ->orderBy('nama')
                ->get(),
            includeUsia: true
        );

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

            $pegawaiQuery = Pegawai::with(['jabatan_aktif.master_jabatan', 'unit_kerja'])
                ->whereYear('tgl_lahir', '>=', $tahunLahirMin)
                ->whereYear('tgl_lahir', '<=', $tahunLahirMax);

            if ($this->isAdminScoped($request)) {
                $pegawaiQuery->where('unit_kerja_id', $request->user()->unit_kerja_id);
            }

            $pegawaiList = $pegawaiQuery
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
