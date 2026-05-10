<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DUK - {{ $unitKerja->nama_unit }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff; }
        .page { padding: 14mm 10mm; }

        /* Kop */
        .kop { text-align: center; margin-bottom: 10px; }
        .kop .title  { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop .sub    { font-size: 9px; margin-top: 2px; text-transform: uppercase; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        th, td { border: 1px solid #555; padding: 3px 4px; vertical-align: top; }
        thead th { background-color: #e5e7eb; text-align: center; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-center { text-align: center; }
        .text-muted  { color: #6b7280; }
        .col-no   { width: 18px; text-align: center; }
        .col-nama { min-width: 80px; }
        .col-nip  { min-width: 80px; white-space: nowrap; }
        .col-gol  { width: 75px; text-align: center; }
        .col-tmt  { width: 58px; text-align: center; white-space: nowrap; }
        .col-jab  { min-width: 90px; }
        .col-esl  { width: 28px; text-align: center; }
        .col-mk   { width: 28px; text-align: center; }
        .col-pend { min-width: 75px; }
        .col-tls  { width: 55px; text-align: center; white-space: nowrap; }

        /* Footer */
        .footer { margin-top: 16px; font-size: 8.5px; color: #555; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { size: A3 landscape; margin: 8mm; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Print controls --}}
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

    {{-- Kop --}}
    <div class="kop">
        <div class="title">DAFTAR URUT KEPANGKATAN</div>
        <div class="sub">
            APARATUR SIPIL NEGARA (ASN) PADA {{ strtoupper($unitKerja->nama_unit) }}
            @if ($instansi)
                {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
            @endif
        </div>
        <div class="sub">TAHUN {{ $tahun }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">NO</th>
                <th colspan="2">NAMA</th>
                <th colspan="2">PANGKAT TERAKHIR</th>
                <th colspan="3">JABATAN</th>
                <th colspan="2">MK GOL</th>
                <th colspan="2">PEND AKHIR</th>
            </tr>
            <tr>
                <th class="col-nama">NAMA</th>
                <th class="col-nip">NIP</th>
                <th class="col-gol">GOL/RUANG</th>
                <th class="col-tmt">TMT</th>
                <th class="col-jab">NAMA</th>
                <th class="col-tmt">TMT</th>
                <th class="col-esl">ESL</th>
                <th class="col-mk">THN</th>
                <th class="col-mk">BLN</th>
                <th class="col-pend">ASAL / TK</th>
                <th class="col-tls">TLS</th>
            </tr>
            <tr style="background:#f3f4f6;">
                @foreach (range(1, 12) as $n)
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
                    <td class="col-nama">
                        <strong>{{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}</strong><br>
                        <span class="text-muted">{{ $pegawai->tmpt_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') }}</span>
                    </td>
                    <td class="col-nip">{{ $pegawai->nip }}</td>
                    <td class="text-center">
                        @if ($pangkat)
                            {{ $pangkat->master_pangkat->nama_pangkat ?? '-' }}<br>
                            <span class="text-muted">({{ $pangkat->master_golongan->nama_golongan ?? '-' }})</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $pangkat && $pangkat->tmt_pangkat_mulai
                            ? \Carbon\Carbon::parse($pangkat->tmt_pangkat_mulai)->format('Y-m-d')
                            : '-' }}
                    </td>
                    <td>{{ $jabatan->master_jabatan->nama_jabatan ?? '-' }}</td>
                    <td class="text-center">
                        {{ $jabatan && $jabatan->tmt_jabatan_mulai
                            ? \Carbon\Carbon::parse($jabatan->tmt_jabatan_mulai)->format('Y-m-d')
                            : '-' }}
                    </td>
                    <td class="text-center">{{ $jabatan->master_eselon->nama_eselon ?? '-' }}</td>
                    <td class="text-center">{{ $pegawai->mk_thn }}</td>
                    <td class="text-center">{{ $pegawai->mk_bln }}</td>
                    <td>
                        @if ($pendidikan)
                            {{ $pendidikan->nama_sekolah_universitas ?? '-' }}<br>
                            <span class="text-muted">{{ $pendidikan->jenjang_pendidikan }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($pendidikan)
                            {{ isset($pendidikan->tgl_ijazah)
                                ? \Carbon\Carbon::parse($pendidikan->tgl_ijazah)->format('Y-m-d')
                                : ($pendidikan->thn_selesai ?? '-') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding:16px; color:#9ca3af;">
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
