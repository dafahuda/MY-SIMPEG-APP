<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bezetting PNS - {{ $unitKerja->nama_unit }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff; }
        .page { padding: 14mm 12mm; }

        .kop { text-align: center; margin-bottom: 12px; }
        .kop .title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop .sub   { font-size: 9px; margin-top: 2px; text-transform: uppercase; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th, td { border: 1px solid #555; padding: 3px 5px; vertical-align: top; }
        thead th { background-color: #e5e7eb; text-align: center; font-weight: bold; font-size: 8.5px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-center { text-align: center; }
        .text-muted  { color: #6b7280; }

        .col-no   { width: 20px; text-align: center; }
        .col-nama { min-width: 100px; }
        .col-nip  { min-width: 90px; white-space: nowrap; }
        .col-gol  { width: 90px; text-align: center; }
        .col-jab  { min-width: 120px; }
        .col-pend { width: 60px; text-align: center; }
        .col-usia { width: 45px; text-align: center; }

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
        <div class="title">DAFTAR BEZETTING PNS</div>
        <div class="sub">
            PADA {{ strtoupper($unitKerja->nama_unit) }}
            @if ($instansi)
                {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
            @endif
        </div>
        <div class="sub">
            PERIODE BULAN {{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }} TAHUN {{ $tahun }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-nama">NAMA<br><span style="font-weight:normal;">TEMPAT TANGGAL LAHIR</span></th>
                <th class="col-nip">NIP</th>
                <th class="col-gol">PANGKAT<br><span style="font-weight:normal;">GOL/RUANG</span></th>
                <th class="col-jab">JABATAN</th>
                <th class="col-pend">PEND.<br><span style="font-weight:normal;">TERAKHIR</span></th>
                <th class="col-usia">USIA<br><span style="font-weight:normal;">(THN)</span></th>
            </tr>
            <tr style="background:#f3f4f6;">
                @foreach (range(1, 7) as $n)
                    <td class="text-center" style="font-weight:600; color:#6b7280;">{{ $n }}</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($pegawaiList as $index => $pegawai)
                @php
                    $pangkat    = $pegawai->pangkat_terakhir;
                    $jabatan    = $pegawai->jabatan_aktif;
                    $pendidikan = $pegawai->pendidikan_terakhir;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}</strong><br>
                        <span class="text-muted">
                            {{ $pegawai->tmpt_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') }}
                        </span>
                    </td>
                    <td>{{ $pegawai->nip }}</td>
                    <td class="text-center">
                        @if ($pangkat)
                            {{ $pangkat->master_pangkat->nama_pangkat ?? '-' }}<br>
                            <span class="text-muted">{{ $pangkat->master_golongan->nama_golongan ?? '-' }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $jabatan->master_jabatan->nama_jabatan ?? '-' }}</td>
                    <td class="text-center">{{ $pendidikan->jenjang_pendidikan ?? '-' }}</td>
                    <td class="text-center">{{ $pegawai->usia }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding:16px; color:#9ca3af;">
                        Tidak ada data pegawai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <strong>Total Pegawai: {{ $pegawaiList->count() }} orang</strong>
    </div>

</div>
</body>
</html>
