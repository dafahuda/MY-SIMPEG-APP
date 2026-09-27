<?php

namespace App\Http\Controllers;

use App\Models\InstansiLembaga;
use App\Models\MasterEselon;
use App\Models\MasterGolongan;
use App\Models\MasterJabatan;
use App\Models\MasterPangkat;
use App\Models\Pangkat;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\UnitKerja;
use App\Services\LaporanPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Unduh PDF berkop untuk laporan rekapitulasi.
 * Query identik dengan RekapitulasiController agar angka di layar
 * dan di PDF selalu sama.
 */
class RekapitulasiPdfController extends Controller
{
    public function __construct(private LaporanPdfService $pdfService)
    {
    }

    public function unduh(Request $request, string $jenis)
    {
        // Pegawai tidak berhak mengunduh rekapitulasi (data lintas pegawai)
        if (Auth::user()->role === 'pegawai') {
            abort(403, 'Rekapitulasi hanya untuk admin dan superadmin.');
        }

        [$judul, $kolom, $baris] = match ($jenis) {
            'opd_skpd_unit_kerja', 'unit_kerja' => $this->rekapUnitKerja(),
            'golongan' => $this->rekapGolongan(),
            'pangkat' => $this->rekapPangkat(),
            'jabatan' => $this->rekapJabatan(),
            'eselon' => $this->rekapEselon(),
            'status_kepegawaian' => $this->rekapStatusKepegawaian(),
            'agama' => $this->rekapAgama(),
            'jenis_kelamin' => $this->rekapJenisKelamin(),
            'status_pernikahan' => $this->rekapStatusPernikahan(),
            'pendidikan_terakhir' => $this->rekapPendidikanAkhir(),
            default => abort(404, 'Jenis rekapitulasi tidak dikenal.'),
        };

        $pdf = $this->pdfService->generate([
            'judul' => $judul,
            'subjudul' => 'Rekapitulasi Data Kepegawaian',
            'kolom' => $kolom,
            'baris' => $baris,
        ]);

        $namaFile = 'rekapitulasi-' . str_replace('_', '-', $jenis) . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($namaFile);
    }

    private function rekapUnitKerja(): array
    {
        $unitKerja = UnitKerja::withCount('pegawai')->get();

        $baris = $unitKerja->map(fn ($u) => [
            'sel' => [$u->nama_unit, $u->pegawai_count],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $unitKerja->sum('pegawai_count')], 'total' => true];

        return ['Rekapitulasi Pegawai per Unit Kerja', ['Unit Kerja', 'Jumlah'], $baris];
    }

    private function rekapGolongan(): array
    {
        $golongan = MasterGolongan::withCount('pegawai')->get();

        $baris = $golongan->map(fn ($g) => [
            'sel' => [$g->nama_golongan, $g->pegawai_count],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $golongan->sum('pegawai_count')], 'total' => true];

        return ['Rekapitulasi Pegawai per Golongan', ['Golongan', 'Jumlah'], $baris];
    }

    private function rekapPangkat(): array
    {
        $pangkat = MasterPangkat::withCount('pegawai')->get();

        $baris = $pangkat->map(fn ($p) => [
            'sel' => [$p->nama_pangkat, $p->pegawai_count],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $pangkat->sum('pegawai_count')], 'total' => true];

        return ['Rekapitulasi Pegawai per Pangkat', ['Pangkat', 'Jumlah'], $baris];
    }

    private function rekapJabatan(): array
    {
        $jabatan = MasterJabatan::withCount('pegawai')->get();

        $baris = $jabatan->map(fn ($j) => [
            'sel' => [$j->nama_jabatan, $j->pegawai_count],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $jabatan->sum('pegawai_count')], 'total' => true];

        return ['Rekapitulasi Pegawai per Jabatan', ['Jabatan', 'Jumlah'], $baris];
    }

    private function rekapEselon(): array
    {
        $eselon = MasterEselon::withCount('pegawai')->get();

        $baris = $eselon->map(fn ($e) => [
            'sel' => [$e->nama_eselon, $e->pegawai_count],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $eselon->sum('pegawai_count')], 'total' => true];

        return ['Rekapitulasi Pegawai per Eselon', ['Eselon', 'Jumlah'], $baris];
    }

    private function rekapStatusKepegawaian(): array
    {
        $data = Pegawai::selectRaw('status_kepegawaian, count(*) as jumlah')
            ->groupBy('status_kepegawaian')->get();

        $baris = $data->map(fn ($d) => [
            'sel' => [$d->status_kepegawaian ?? '(tidak diisi)', $d->jumlah],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $data->sum('jumlah')], 'total' => true];

        return ['Rekapitulasi Pegawai per Status Kepegawaian', ['Status Kepegawaian', 'Jumlah'], $baris];
    }

    private function rekapAgama(): array
    {
        $data = Pegawai::selectRaw('agama, count(*) as jumlah')
            ->groupBy('agama')->get();

        $baris = $data->map(fn ($d) => [
            'sel' => [$d->agama ?? '(tidak diisi)', $d->jumlah],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $data->sum('jumlah')], 'total' => true];

        return ['Rekapitulasi Pegawai per Agama', ['Agama', 'Jumlah'], $baris];
    }

    private function rekapJenisKelamin(): array
    {
        $data = Pegawai::selectRaw('jenis_kelamin, count(*) as jumlah')
            ->groupBy('jenis_kelamin')->get();

        $baris = $data->map(fn ($d) => [
            'sel' => [$d->jenis_kelamin ?? '(tidak diisi)', $d->jumlah],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $data->sum('jumlah')], 'total' => true];

        return ['Rekapitulasi Pegawai per Jenis Kelamin', ['Jenis Kelamin', 'Jumlah'], $baris];
    }

    private function rekapStatusPernikahan(): array
    {
        $data = Pegawai::selectRaw('status_pernikahan, count(*) as jumlah')
            ->groupBy('status_pernikahan')->get();

        $baris = $data->map(fn ($d) => [
            'sel' => [$d->status_pernikahan ?? '(tidak diisi)', $d->jumlah],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $data->sum('jumlah')], 'total' => true];

        return ['Rekapitulasi Pegawai per Status Pernikahan', ['Status Pernikahan', 'Jumlah'], $baris];
    }

    private function rekapPendidikanAkhir(): array
    {
        // Identik dengan RekapitulasiController::rekapPendidikanAkhir:
        // gabung pendidikan sekolah + lanjut, ambil jenjang tertinggi per pegawai
        $jenjangOrder = ['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

        $semuaRiwayat = RiwayatPendidikanSekolah::select('pegawai_id', 'jenjang_pendidikan')->get()
            ->concat(\App\Models\RiwayatPendidikanLanjut::select('pegawai_id', 'jenjang_pendidikan')->get());

        $grouped = $semuaRiwayat
            ->groupBy('pegawai_id')
            ->map(function ($records) use ($jenjangOrder) {
                return $records->sortByDesc(function ($r) use ($jenjangOrder) {
                    $idx = array_search(strtoupper(trim($r->jenjang_pendidikan)), $jenjangOrder);

                    return $idx !== false ? $idx : -1;
                })->first()->jenjang_pendidikan;
            })
            ->groupBy(fn ($j) => $j)
            ->map(fn ($items, $jenjang) => [
                'jenjang_pendidikan' => $jenjang,
                'jumlah' => $items->count(),
            ])
            ->values();

        $baris = $grouped->map(fn ($d) => [
            'sel' => [$d['jenjang_pendidikan'], $d['jumlah']],
        ])->all();
        $baris[] = ['sel' => ['JUMLAH', $grouped->sum('jumlah')], 'total' => true];

        return ['Rekapitulasi Pegawai per Pendidikan Terakhir', ['Pendidikan', 'Jumlah'], $baris];
    }
}
