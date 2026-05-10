<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KGBExport implements FromCollection, WithHeadings
{
    protected $periode;
    protected $tahunTarget;

    public function __construct($periode = 'tahun_ini', $tahunTarget = null)
    {
        $this->periode     = $periode;
        $this->tahunTarget = $tahunTarget ?? (int) date('Y');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $tahunTarget = $this->tahunTarget;

        $pegawaiKgb = Pegawai::whereNotNull('tmt_pns')
            ->get()
            ->filter(function ($pegawai) use ($tahunTarget) {
                $tmt          = \Carbon\Carbon::parse($pegawai->tmt_pns);
                $selisihTahun = $tahunTarget - $tmt->year;

                return $selisihTahun > 0 && $selisihTahun % 2 === 0;
            });

        $no = 1;

        return $pegawaiKgb->map(function ($pegawai) use ($tahunTarget, &$no) {
            $tmt    = \Carbon\Carbon::parse($pegawai->tmt_pns);
            $periode = $tahunTarget . '-' . $tmt->format('m-d');

            $nama = trim(
                ($pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '') .
                $pegawai->nama .
                ($pegawai->gelar ? ', ' . $pegawai->gelar : '')
            );

            return [
                'No'           => $no++,
                'NIP'          => $pegawai->nip,
                'Nama'         => $nama,
                'Tempat Lahir' => $pegawai->tmpt_lahir,
                'Tgl Lahir'    => $pegawai->tgl_lahir
                    ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('d-m-Y')
                    : '-',
                'Jenis Kelamin' => $pegawai->jenis_kelamin === 'laki-laki' ? 'Laki-laki' : 'Perempuan',
                'No. Telp'     => $pegawai->no_hp,
                'TMT PNS'      => $tmt->format('d-m-Y'),
                'Periode KGB'  => $periode,
            ];
        })->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama',
            'Tempat Lahir',
            'Tgl Lahir',
            'Jenis Kelamin',
            'No. Telp',
            'TMT PNS',
            'Periode KGB',
        ];
    }
}
