<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keadaan Pegawai - {{ $unitKerja->nama_unit }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff; }
        .page { padding: 14mm 12mm; }

        .kop { text-align: center; margin-bottom: 10px; }
        .kop .title { font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .kop .sub   { font-size: 9px; margin-top: 2px; text-transform: uppercase; }
        .periode    { font-size: 9px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }

        table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        th, td { border: 1px solid #555; padding: 3px 4px; vertical-align: middle; }
        thead th { background-color: #e5e7eb; text-align: center; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        .section-row td { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; }
        .text-center { text-align: center; }
        .text-muted  { color: #6b7280; }
        .col-no   { width: 18px; text-align: center; }
        .col-jenis { min-width: 120px; }
        .col-num  { width: 28px; text-align: center; }
        .col-jml  { width: 36px; text-align: center; font-weight: bold; }

        .footer { margin-top: 14px; font-size: 8.5px; color: #555; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { size: A4 landscape; margin: 10mm; }
        }
    </style>
</head>
<body>
<div class="page">

    <div class="no-print" style="margin-bottom:12px; display:flex; gap:8px;">
        <button onclick="window.print()"
            style="padding:6px 16px; background:#16a34a; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px;">
            🖨 Print
        </button>
        <button onclick="window.history.back()"
            style="padding:6px 16px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:12px;">
            ← Kembali
        </button>
    </div>

    <div class="kop">
        <div class="title">LAPORAN BULANAN KEADAAN PEGAWAI</div>
        <div class="sub">
            DI LINGKUNGAN {{ strtoupper($unitKerja->nama_unit) }}
            @if ($instansi)
                {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
            @endif
        </div>
    </div>

    <div class="periode">
        PERIODE : BULAN {{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }} TAHUN {{ $tahun }}
    </div>

    @php
        $cnt    = $stats['cnt'];
        $sumGol = $stats['sumGol'];
        $dash   = fn($v) => $v > 0 ? $v : '-';

        $rows = [
            ['section' => 'JUMLAH PEGAWAI', 'items' => [
                ['key' => 'laki',      'label' => '- Laki-laki'],
                ['key' => 'perempuan', 'label' => '- Perempuan'],
            ]],
            ['section' => 'JENJANG PENDIDIKAN', 'items' => [
                ['key' => 'sd',      'label' => '- SD/MI'],
                ['key' => 'smp',     'label' => '- SMP/MTS'],
                ['key' => 'sma',     'label' => '- SMK/SMA/MA'],
                ['key' => 'd2d3',    'label' => '- D2/D3'],
                ['key' => 'd4s1',    'label' => '- S1/D4'],
                ['key' => 's2s3',    'label' => '- S2/S3'],
                ['key' => 'profesi', 'label' => '- PROFESI'],
            ]],
        ];
    @endphp

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">NO</th>
                <th rowspan="2" class="col-jenis">JENIS LAPORAN</th>
                <th colspan="5">GOLONGAN</th>
                <th colspan="5">ESELON</th>
            </tr>
            <tr>
                <th class="col-num">I</th>
                <th class="col-num">II</th>
                <th class="col-num">III</th>
                <th class="col-num">IV</th>
                <th class="col-jml">JUMLAH</th>
                <th class="col-num">II</th>
                <th class="col-num">III</th>
                <th class="col-num">IV</th>
                <th class="col-num">V</th>
                <th class="col-jml">STAFF</th>
            </tr>
            <tr style="background:#f3f4f6;">
                @foreach (range(1, 12) as $n)
                    <td class="text-center" style="font-weight:600; color:#6b7280;">{{ $n }}</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($rows as $section)
                <tr class="section-row">
                    <td class="text-center">{{ $no++ }}</td>
                    <td colspan="11">{{ $section['section'] }}</td>
                </tr>
                @foreach ($section['items'] as $item)
                    @php $r = $cnt[$item['key']]; @endphp
                    <tr>
                        <td></td>
                        <td>{{ $item['label'] }}</td>
                        <td class="text-center">{{ $dash($r['I']) }}</td>
                        <td class="text-center">{{ $dash($r['II']) }}</td>
                        <td class="text-center">{{ $dash($r['III']) }}</td>
                        <td class="text-center">{{ $dash($r['IV']) }}</td>
                        <td class="text-center" style="font-weight:bold;">{{ $dash($sumGol($r)) }}</td>
                        <td class="text-center">{{ $dash($r['esl_II'] ?? 0) }}</td>
                        <td class="text-center">{{ $dash($r['esl_III'] ?? 0) }}</td>
                        <td class="text-center">{{ $dash($r['esl_IV'] ?? 0) }}</td>
                        <td class="text-center">{{ $dash($r['esl_V'] ?? 0) }}</td>
                        <td class="text-center">{{ $dash($r['staff'] ?? 0) }}</td>
                    </tr>
                @endforeach
            @endforeach

            {{-- Mutasi --}}
            <tr class="section-row">
                <td class="text-center">{{ $no++ }}</td>
                <td colspan="11">MUTASI PEGAWAI</td>
            </tr>
            <tr>
                <td></td>
                <td>- Masuk</td>
                <td colspan="10" class="text-center">{{ $stats['mutasi_masuk'] > 0 ? $stats['mutasi_masuk'] : '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td>- Keluar</td>
                <td colspan="10" class="text-center">{{ $stats['mutasi_keluar'] > 0 ? $stats['mutasi_keluar'] : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <strong>Total Pegawai: {{ $stats['total'] }} orang</strong>
    </div>

</div>
</body>
</html>
