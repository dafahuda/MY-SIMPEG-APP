<?php

namespace App\Exports;

use App\Models\Pegawai;
use App\Support\ExcelSanitizer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KGBExport implements FromCollection, WithHeadings
{
    protected $periode;
    protected $tahunTarget;
    protected $unitKerjaId;

    public function __construct($periode = 'tahun_ini', $tahunTarget = null, $unitKerjaId = null)
    {
        $this->periode     = $periode;
        $this->tahunTarget = $tahunTarget ?? (int) date('Y');
        $this->unitKerjaId = $unitKerjaId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $tahunTarget = $this->tahunTarget;

        $query = Pegawai::whereNotNull('tmt_pns');

        if ($this->unitKerjaId) {
            $query->where('unit_kerja_id', $this->unitKerjaId);
        }

        $pegawaiKgb = $query->get()
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
                'NIP'          => ExcelSanitizer::cell($pegawai->nip),
                'Nama'         => ExcelSanitizer::cell($nama),
                'Tempat Lahir' => ExcelSanitizer::cell($pegawai->tmpt_lahir),
                'Tgl Lahir'    => $pegawai->tgl_lahir
                    ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('d-m-Y')
                    : '-',
                'Jenis Kelamin' => $pegawai->jenis_kelamin === 'laki-laki' ? 'Laki-laki' : 'Perempuan',
                'No. Telp'     => ExcelSanitizer::cell($pegawai->no_hp),
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
