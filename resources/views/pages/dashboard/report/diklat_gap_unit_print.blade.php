@php
    use App\Support\DiklatGlossary;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gap Diklat per Unit/Tahun - {{ $selectedUnitKerja?->nama_unit ?? 'Semua Unit' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff; }
        .page { padding: 14mm 12mm; }

        .kop { text-align: center; margin-bottom: 12px; }
        .kop .title { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop .sub { font-size: 9px; margin-top: 2px; text-transform: uppercase; }

        .meta { margin-bottom: 10px; font-size: 9px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        th, td { border: 1px solid #555; padding: 3px 4px; vertical-align: top; }
        thead th { background-color: #e5e7eb; text-align: center; font-weight: bold; text-transform: uppercase; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .no-print { margin-bottom: 12px; display: flex; gap: 8px; }
        .footer { margin-top: 16px; font-size: 8.5px; color: #555; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { size: A4 landscape; margin: 10mm; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="no-print">
        <button onclick="window.print()" style="padding:6px 16px; background:#16a34a; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px;">🖨 Cetak</button>
        <button onclick="window.history.back()" style="padding:6px 16px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px;">← Kembali</button>
    </div>

    <div class="kop">
        <div class="title">Laporan Kesenjangan Diklat Per Unit/Tahun</div>
        <div class="sub">
            {{ strtoupper($selectedUnitKerja?->nama_unit ?? 'Semua Unit') }}
            @if ($selectedTahunRencana)
                - TAHUN RENCANA {{ $selectedTahunRencana }}
            @endif
            @if ($selectedTahunRealisasi)
                - TAHUN REALISASI {{ $selectedTahunRealisasi }}
            @endif
        </div>
    </div>

    <div class="meta">
        @if ($selectedUnitKerja)
            Unit: {{ $selectedUnitKerja->nama_unit }}
        @else
            Unit: Semua Unit
        @endif
    </div>

    <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:8px;">
        <tr>
            @foreach ([
                'planned' => ['label' => DiklatGlossary::reportLabel('planned'), 'value' => $summary['planned_count']],
                'realized' => ['label' => DiklatGlossary::reportLabel('realized'), 'value' => $summary['realized_linked_count']],
                'not_realized' => ['label' => DiklatGlossary::reportLabel('not_realized'), 'value' => $summary['not_realized_count']],
                'out_of_plan' => ['label' => DiklatGlossary::reportLabel('out_of_plan'), 'value' => $summary['out_of_plan_count']],
                'cross_year_realized' => ['label' => DiklatGlossary::reportLabel('cross_year_realized'), 'value' => $summary['cross_year_realized_count']],
                'planned_hours' => ['label' => DiklatGlossary::reportLabel('planned_hours'), 'value' => $summary['planned_hours']],
                'realized_linked_hours' => ['label' => DiklatGlossary::reportLabel('realized_linked_hours'), 'value' => $summary['realized_linked_hours']],
                'hour_gap' => ['label' => DiklatGlossary::reportLabel('hour_gap'), 'value' => $summary['hour_gap']],
            ] as $key => $metric)
                <td data-testid="diklat-gap-unit-summary-card-{{ $key }}" style="border:1px solid #bbb; padding:6px; text-align:center; width:12.5%;">
                    <div style="font-size:7px; text-transform:uppercase; color:#6b7280;">{{ $metric['label'] }}</div>
                    <div data-testid="diklat-gap-unit-summary-value-{{ $key }}" data-value="{{ $metric['value'] }}" style="font-size:12px; font-weight:bold; margin-top:2px;">{{ number_format($metric['value']) }}</div>
                </td>
            @endforeach
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Pegawai</th>
                <th>Unit</th>
                <th>Tahun Rencana</th>
                <th>Tahun Realisasi</th>
                <th>Nama Rencana</th>
                <th>Nama Realisasi</th>
                <th>Kategori Status</th>
                <th class="text-right">Target Jam</th>
                <th class="text-right">Realisasi Jam</th>
                <th class="text-right">Selisih Jam</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($exportRows as $index => $row)
                <tr data-testid="diklat-gap-unit-print-row-{{ $index }}">
                    <td>{{ $row['pegawai'] }}</td>
                    <td>{{ $row['unit'] }}</td>
                    <td class="text-center">{{ $row['tahun_rencana'] }}</td>
                    <td class="text-center">{{ $row['tahun_realisasi'] }}</td>
                    <td>{{ $row['nama_rencana'] }}</td>
                    <td>{{ $row['nama_realisasi'] }}</td>
                    <td class="text-center">{{ $row['bucket_label'] }}</td>
                    <td class="text-right">{{ number_format($row['target_jam']) }}</td>
                    <td class="text-right">{{ number_format($row['realisasi_jam']) }}</td>
                    <td class="text-right">{{ number_format($row['gap_jam']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding:16px; color:#9ca3af;">Tidak ada data agregat unit kerja.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <strong>Total Baris: {{ $exportRows->count() }} baris</strong>
    </div>
</div>
</body>
</html>
