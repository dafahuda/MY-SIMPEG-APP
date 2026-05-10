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

    public function rekapStatusKepegawaian()
    {
        $statusKepegawaian = Pegawai::selectRaw("status_kepegawaian, count(*) as jumlah")
                                     ->groupBy('status_kepegawaian')
                                     ->get();

        $chartCategories = $statusKepegawaian->pluck("status_kepegawaian")->toArray();
        $chartData = $statusKepegawaian->pluck('jumlah')->toArray();

        // Pegawai yang belum memiliki status kepegawaian
        $pegawaiTanpaData = Pegawai::where(function ($q) {
            $q->whereNull('status_kepegawaian')->orWhere('status_kepegawaian', '');
        })->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapStatusKepegawaian", [
            'statusKepegawaian' => $statusKepegawaian,
            'chartCategories'   => $chartCategories,
            'chartData'         => $chartData,
            'pegawaiTanpaData'  => $pegawaiTanpaData,
            'labelData'         => 'data status kepegawaian',
            'instansi' => $instansi
        ]);
    }

    public function rekapAgama()
    {
        $agama = Pegawai::selectRaw("agama, count(*) as jumlah")
                         ->groupBy("agama")
                         ->get();

        $chartCategories = $agama->pluck("agama")->toArray();
        $chartData = $agama->pluck("jumlah")->toArray();

        // Pegawai yang belum mengisi agama
        $pegawaiTanpaData = Pegawai::where(function ($q) {
            $q->whereNull('agama')->orWhere('agama', '');
        })->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapAgama", [
            'agama'            => $agama,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data agama',
            'instansi' => $instansi
        ]);
    }

    public function rekapJenisKelamin()
    {
        $jenisKelamin = Pegawai::selectRaw("jenis_kelamin, count(*) as jumlah")
                                ->groupBy("jenis_kelamin")
                                ->get();

        $chartCategories = $jenisKelamin->pluck("jenis_kelamin")->toArray();
        $chartData = $jenisKelamin->pluck("jumlah")->toArray();

        // Pegawai yang belum mengisi jenis kelamin
        $pegawaiTanpaData = Pegawai::where(function ($q) {
            $q->whereNull('jenis_kelamin')->orWhere('jenis_kelamin', '');
        })->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapJenisKelamin", [
            'jenisKelamin'     => $jenisKelamin,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data jenis kelamin',
            'instansi' => $instansi
        ]);
    }

    public function rekapStatusPernikahan()
    {
        $statusPernikahan = Pegawai::selectRaw("status_pernikahan, count(*) as jumlah")
                                   ->groupBy("status_pernikahan")
                                   ->get();

        $chartCategories = $statusPernikahan->pluck("status_pernikahan")->toArray();
        $chartData = $statusPernikahan->pluck("jumlah")->toArray();

        // Pegawai yang belum mengisi status pernikahan
        $pegawaiTanpaData = Pegawai::where(function ($q) {
            $q->whereNull('status_pernikahan')->orWhere('status_pernikahan', '');
        })->count();

        $instansi = InstansiLembaga::first();

        return view("pages.dashboard.rekapitulasi.rekapStatusNikah", [
            'statusPernikahan' => $statusPernikahan,
            'chartCategories'  => $chartCategories,
            'chartData'        => $chartData,
            'pegawaiTanpaData' => $pegawaiTanpaData,
            'labelData'        => 'data status pernikahan',
            'instansi' => $instansi
        ]);
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
