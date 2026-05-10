<?php

namespace App\Exports;

use App\Models\Tpp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TppExport implements FromCollection, WithHeadings
{
    protected $periode;
    protected $tahun;

    public function __construct($periode = null, $tahun = null)
    {
        $this->periode = $periode;
        $this->tahun   = $tahun;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Tpp::with('pegawai');

        if ($this->periode) {
            $query->where('periode', $this->periode);
        }

        if ($this->tahun) {
            $query->where('tahun', $this->tahun);
        }

        return $query->orderBy('id')->get()->map(function ($tpp) {
            return [
                'Nama Pegawai'              => $tpp->pegawai->nama ?? '-',
                'Periode'                   => $tpp->periode,
                'Tahun'                     => $tpp->tahun,
                'Jml Hari Kerja'            => $tpp->jml_hari_kerja,
                'Tidak Ada Produktifitas'   => $tpp->tidak_ada_produktifitas,
                'Terlambat 1-30 Mnt'        => $tpp->terlambat_1_30,
                'Terlambat 31-60 Mnt'       => $tpp->terlambat_31_60,
                'Terlambat 61-90 Mnt'       => $tpp->terlambat_61_90,
                'Terlambat >90 Mnt'         => $tpp->terlambat_91_lebih,
                'Pulang Awal 1-30 Mnt'      => $tpp->pulang_awal_1_30,
                'Pulang Awal 31-60 Mnt'     => $tpp->pulang_awal_31_60,
                'Pulang Awal 61-90 Mnt'     => $tpp->pulang_awal_61_90,
                'Pulang Awal >90 Mnt'       => $tpp->pulang_awal_91_lebih,
                'Tidak Masuk Kerja'         => $tpp->tidak_masuk_kerja,
                'Nilai Basic TPP'           => $tpp->nilai_basic_tpp,
                'Pengurangan Produktifitas' => $tpp->pengurangan_produktifitas,
                'Pengurangan Disiplin'      => $tpp->pengurangan_disiplin,
                'TPP Diterima'              => $tpp->tpp_diterima,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Periode',
            'Tahun',
            'Jml Hari Kerja',
            'Tidak Ada Produktifitas',
            'Terlambat 1-30 Mnt',
            'Terlambat 31-60 Mnt',
            'Terlambat 61-90 Mnt',
            'Terlambat >90 Mnt',
            'Pulang Awal 1-30 Mnt',
            'Pulang Awal 31-60 Mnt',
            'Pulang Awal 61-90 Mnt',
            'Pulang Awal >90 Mnt',
            'Tidak Masuk Kerja',
            'Nilai Basic TPP',
            'Pengurangan Produktifitas',
            'Pengurangan Disiplin',
            'TPP Diterima',
        ];
    }
}
