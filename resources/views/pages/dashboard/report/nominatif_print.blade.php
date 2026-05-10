<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nominatif PNS - {{ $unitKerja->nama_unit }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #111; background: #fff; }
        .page { padding: 16mm 12mm; }

        /* Kop */
        .kop { text-align: center; margin-bottom: 12px; }
        .kop .title { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop .subtitle { font-size: 10px; margin-top: 2px; }
        .unit-name { font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        th, td { border: 1px solid #555; padding: 3px 4px; vertical-align: top; }
        thead th { background-color: #e5e7eb; text-align: center; font-weight: bold; font-size: 8.5px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .col-no      { width: 20px; text-align: center; }
        .col-nama    { min-width: 90px; }
        .col-ttl     { min-width: 80px; white-space: nowrap; }
        .col-jk      { width: 50px; text-align: center; }
        .col-gol     { width: 80px; text-align: center; }
        .col-tmt     { width: 60px; text-align: center; white-space: nowrap; }
        .col-jabatan { min-width: 100px; }
        .col-esl     { width: 30px; text-align: center; }
        .col-pend    { min-width: 80px; }
        .col-alamat  { min-width: 90px; }
        .text-muted  { color: #6b7280; }

        /* Footer */
        .footer { margin-top: 20px; font-size: 9px; color: #555; }
        .total  { font-weight: bold; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { size: A3 landscape; margin: 10mm; }
        }
    </style>
</head>
<body>
    <div class="page">

        {{-- Print button (hidden on print) --}}
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
            <div class="title">DAFTAR NOMINATIF PEGAWAI NEGERI SIPIL</div>
            <div class="subtitle">
                PER {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('d F Y')) }}
            </div>
        </div>

        <div class="unit-name">
            {{ $unitKerja->nama_unit }}
            @if ($instansi)
                {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2" class="col-no">NO</th>
                    <th colspan="2">NAMA</th>
                    <th rowspan="2" class="col-jk">JNS<br>KELAMIN</th>
                    <th colspan="2">PANGKAT TERAKHIR</th>
                    <th colspan="3">JABATAN</th>
                    <th rowspan="2" class="col-pend">PEND / JURUSAN / LULUS</th>
                    <th rowspan="2" class="col-alamat">ALAMAT & NO TELP</th>
                </tr>
                <tr>
                    <th class="col-nama">NAMA</th>
                    <th class="col-ttl">TTL / NIP / AGAMA</th>
                    <th class="col-gol">GOL/RUANG</th>
                    <th class="col-tmt">TMT</th>
                    <th class="col-jabatan">NAMA</th>
                    <th class="col-tmt">TMT</th>
                    <th class="col-esl">ESL</th>
                </tr>
                <tr style="background:#f3f4f6;">
                    @foreach (range(1, 10) as $n)
                        <td class="text-center" style="font-weight:600; color:#6b7280;">{{ $n }}</td>
                    @endforeach
                    {{-- kolom 10 sudah rowspan --}}
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
                            <strong>{{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}</strong>
                        </td>
                        <td class="col-ttl">
                            {{ $pegawai->tmpt_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') }}<br>
                            {{ $pegawai->nip }}<br>
                            {{ $pegawai->nik }}<br>
                            {{ $pegawai->agama }}
                        </td>
                        <td class="text-center">
                            {{ $pegawai->jenis_kelamin === 'laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                        </td>
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
                        <td>
                            @if ($pendidikan)
                                {{ $pendidikan->jenjang_pendidikan }}<br>
                                {{ $pendidikan->nama_sekolah_universitas ?? '' }}<br>
                                <span class="text-muted">{{ $pendidikan->jurusan ?? '' }}</span><br>
                                <span class="text-muted">
                                    {{ isset($pendidikan->tgl_ijazah)
                                        ? \Carbon\Carbon::parse($pendidikan->tgl_ijazah)->format('Y-m-d')
                                        : ($pendidikan->thn_selesai ?? '') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{ $pegawai->alamat }}<br>
                            <span class="text-muted">{{ $pegawai->no_hp }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center" style="padding:16px; color:#9ca3af;">
                            Tidak ada data pegawai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <span class="total">Total Pegawai: {{ $pegawaiList->count() }} orang</span>
        </div>

    </div>
</body>
</html>
