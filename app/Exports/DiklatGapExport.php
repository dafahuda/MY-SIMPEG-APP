<?php

namespace App\Exports;

use App\Support\ExcelSanitizer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DiklatGapExport implements FromCollection, WithHeadings
{
    public function __construct(protected Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows->map(function (array $row): array {
            return [
                'Pegawai' => ExcelSanitizer::cell($row['pegawai'] ?? '-'),
                'Unit' => ExcelSanitizer::cell($row['unit'] ?? '-'),
                'Tahun Rencana' => $row['tahun_rencana'] ?? '-',
                'Tahun Realisasi' => $row['tahun_realisasi'] ?? '-',
                'Nama Rencana' => ExcelSanitizer::cell($row['nama_rencana'] ?? '-'),
                'Nama Realisasi' => ExcelSanitizer::cell($row['nama_realisasi'] ?? '-'),
                'Bucket Status' => ExcelSanitizer::cell($row['bucket_label'] ?? $row['bucket_status'] ?? '-'),
                'Target Jam' => $row['target_jam'] ?? 0,
                'Realisasi Jam' => $row['realisasi_jam'] ?? 0,
                'Gap Jam' => $row['gap_jam'] ?? 0,
            ];
        })->values();
    }

    public function headings(): array
    {
        return [
            'Pegawai',
            'Unit',
            'Tahun Rencana',
            'Tahun Realisasi',
            'Nama Rencana',
            'Nama Realisasi',
            'Bucket Status',
            'Target Jam',
            'Realisasi Jam',
            'Gap Jam',
        ];
    }
}
