<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\MasterGolongan;
use App\Models\MasterPangkat;
use App\Models\MasterJabatan;
use App\Models\MasterEselon;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\RiwayatPendidikanLanjut;
use App\Models\Pangkat;
use App\Models\Jabatan;
use App\Models\InstansiLembaga;

class RekapitulasiController extends Controller
{
    public function rekapUnitKerja()
    {
        $unitKerja = UnitKerja::withCount('pegawai')->get();

        $chartCategories = $unitKerja->pluck('nama_unit')->toArray();
        $chartData = $unitKerja->pluck('pegawai_count')->toArray();

        // Pegawai yang belum memiliki unit kerja
        $pegawaiTanpaData = Pegawai::whereNull('unit_kerja_id')->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapUnitKerja", [
            'unitKerja'        => $unitKerja,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data opd / skpd / unit kerja',
            'instansi'         => $instansi,
        ]);
    }

    public function rekapGolongan()
    {
        $golongan = MasterGolongan::withCount('pegawai')->get();

        $chartCategories = $golongan->pluck('nama_golongan')->toArray();
        $chartData = $golongan->pluck('pegawai_count')->toArray();

        // Pegawai yang belum memiliki riwayat pangkat (golongan ada di pangkat)
        $pegawaiDenganPangkat = Pangkat::distinct('pegawai_id')->pluck('pegawai_id');
        $pegawaiTanpaData = Pegawai::whereNotIn('id', $pegawaiDenganPangkat)->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapGolongan", [
            'golongan'         => $golongan,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'instansi' => $instansi,
            'labelData'        => 'data golongan',
        ]);
    }

    public function rekapPangkat()
    {
        $pangkat = MasterPangkat::withCount("pegawai")->get();

        $chartCategories = $pangkat->pluck('nama_pangkat')->toArray();
        $chartData = $pangkat->pluck('pegawai_count')->toArray();

        // Pegawai yang belum memiliki riwayat pangkat
        $pegawaiDenganPangkat = Pangkat::distinct('pegawai_id')->pluck('pegawai_id');
        $pegawaiTanpaData = Pegawai::whereNotIn('id', $pegawaiDenganPangkat)->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapPangkat", [
            'pangkat'          => $pangkat,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data pangkat',
            'instansi' => $instansi
        ]);
    }

    public function rekapJabatan()
    {
        $jabatan = MasterJabatan::withCount("pegawai")->get();

        $chartCategories = $jabatan->pluck('nama_jabatan')->toArray();
        $chartData = $jabatan->pluck('pegawai_count')->toArray();

        // Pegawai yang belum memiliki riwayat jabatan
        $pegawaiDenganJabatan = Jabatan::distinct('pegawai_id')->pluck('pegawai_id');
        $pegawaiTanpaData = Pegawai::whereNotIn('id', $pegawaiDenganJabatan)->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapJabatan", [
            'jabatan'          => $jabatan,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data jabatan',
            'instansi' => $instansi
        ]);
    }

    public function rekapEselon()
    {
        $eselon = MasterEselon::withCount("pegawai")->get();

        $chartCategories = $eselon->pluck('nama_eselon')->toArray();
        $chartData = $eselon->pluck("pegawai_count")->toArray();

        // Pegawai yang belum memiliki riwayat jabatan (eselon ada di jabatan)
        $pegawaiDenganJabatan = Jabatan::distinct('pegawai_id')->pluck('pegawai_id');
        $pegawaiTanpaData = Pegawai::whereNotIn('id', $pegawaiDenganJabatan)->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapEselon", [
            'eselon'           => $eselon,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data eselon',
            'instansi' => $instansi
        ]);
    }

        /**
     * Rekap generik: hitung jumlah pegawai per nilai kolom sederhana
     * (status_kepegawaian, agama, jenis_kelamin, status_pernikahan).
     */
    private function rekapKolomSederhana(string $kolom, string $labelData, string $namaView, string $namaVar)
    {
        $rekap = Pegawai::selectRaw("{$kolom}, count(*) as jumlah")
                        ->groupBy($kolom)
                        ->get();

        $chartCategories = $rekap->pluck($kolom)->toArray();
        $chartData       = $rekap->pluck('jumlah')->toArray();

        $pegawaiTanpaData = Pegawai::where(function ($q) use ($kolom) {
            $q->whereNull($kolom)->orWhere($kolom, '');
        })->count();

        return view($namaView, [
            $namaVar           => $rekap,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => $labelData,
            'instansi'         => InstansiLembaga::first(),
        ]);
    }

    public function rekapStatusKepegawaian()
    {
        return $this->rekapKolomSederhana(
            'status_kepegawaian', 'data status kepegawaian',
            'pages.dashboard.rekapitulasi.rekapStatusKepegawaian', 'statusKepegawaian'
        );
    }

    public function rekapAgama()
    {
        return $this->rekapKolomSederhana(
            'agama', 'data agama',
            'pages.dashboard.rekapitulasi.rekapAgama', 'agama'
        );
    }

    public function rekapJenisKelamin()
    {
        return $this->rekapKolomSederhana(
            'jenis_kelamin', 'data jenis kelamin',
            'pages.dashboard.rekapitulasi.rekapJenisKelamin', 'jenisKelamin'
        );
    }

    public function rekapStatusPernikahan()
    {
        return $this->rekapKolomSederhana(
            'status_pernikahan', 'data status pernikahan',
            'pages.dashboard.rekapitulasi.rekapStatusNikah', 'statusPernikahan'
        );
    }

public function rekapPendidikanAkhir()
    {
        $jenjangOrder = ['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

        $semuaRiwayat = RiwayatPendidikanSekolah::select('pegawai_id', 'jenjang_pendidikan')->get();
        $riwayatLanjut = RiwayatPendidikanLanjut::select('pegawai_id', 'jenjang_pendidikan')->get();
        $semuaRiwayat = $semuaRiwayat->concat($riwayatLanjut);

        $pendidikanPerPegawai = $semuaRiwayat
            ->groupBy('pegawai_id')
            ->map(function ($records) use ($jenjangOrder) {
                $tertinggi = $records->sortByDesc(function ($r) use ($jenjangOrder) {
                    $idx = array_search(strtoupper(trim($r->jenjang_pendidikan)), $jenjangOrder);
                    return $idx !== false ? $idx : -1;
                })->first();
                return $tertinggi->jenjang_pendidikan;
            });

        $grouped = $pendidikanPerPegawai
            ->groupBy(fn($j) => $j)
            ->map(fn($items, $jenjang) => [
                'jenjang_pendidikan' => $jenjang,
                'jumlah'             => $items->count(),
            ])
            ->values()
            ->sortByDesc(fn($item) => array_search(strtoupper(trim($item['jenjang_pendidikan'])), $jenjangOrder))
            ->values();

        $chartCategories = $grouped->pluck('jenjang_pendidikan')->toArray();
        $chartData       = $grouped->pluck('jumlah')->toArray();

        // Pegawai yang belum memiliki riwayat pendidikan sama sekali
        $pegawaiDenganPendidikan = $semuaRiwayat->pluck('pegawai_id')->unique();
        $pegawaiTanpaData = Pegawai::whereNotIn('id', $pegawaiDenganPendidikan)->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapPendidikanAkhir", [
            'pendidikanAkhir'  => $grouped,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data pendidikan',
            'instansi' => $instansi
        ]);
    }
}
