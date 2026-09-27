<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pegawai Akan Pensiun</title>
    <style>
        body { font-family: "DejaVu Serif", serif; font-size: 11px; color: #000; margin: 30px 20mm 20px 20mm; }
        table { border-collapse: collapse; width: 100%; }

        .kop { border-bottom: 3px double #000; padding-bottom: 4px; margin-bottom: 14px; text-align: center; }
        .kop .instansi-jenis { font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }
        .kop .instansi-nama { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .kop .instansi-alamat { font-size: 10px; }

        .judul-laporan { text-align: center; margin: 14px 0 4px 0; }
        .judul-laporan h2 { font-size: 13px; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .subjudul { text-align: center; font-size: 11px; margin-bottom: 14px; }

        table.data th, table.data td { border: 1px solid #000; padding: 4px 6px; font-size: 10.5px; }
        table.data thead th { text-align: center; font-weight: bold; background-color: #eee; }
        .tengah { text-align: center; }
        .mono { font-family: "DejaVu Sans Mono", monospace; font-size: 9.5px; }

        .ttd { margin-top: 26px; width: 60%; margin-left: 40%; font-size: 11px; }
        .ttd .tempat-tanggal { margin-bottom: 40px; }
        .ttd .nama-pejabat { font-weight: bold; text-decoration: underline; margin-top: 40px; }
        .ttd .nip { margin-top: 2px; }

        .footer-info { position: fixed; bottom: 12px; left: 20mm; right: 20mm; font-size: 8px; color: #555;
                       border-top: 1px solid #999; padding-top: 3px; }
    </style>
</head>
<body>

    <div class="kop">
        <div class="instansi-jenis">{{ $instansi->jenis_instansi ?? 'PEMERINTAH' }}</div>
        <div class="instansi-nama">{{ $instansi->nama_instansi_lembaga ?? config('app.name') }}</div>
        <div class="instansi-alamat">{{ $instansi->alamat ?? '' }}</div>
    </div>

    <div class="judul-laporan">
        <h2>Daftar Pegawai yang Akan Pensiun</h2>
    </div>
    <div class="subjudul">Periode: {{ $periodeLabel }} ({{ $tahun }}) — Batas Usia Pensiun 58 Tahun</div>

    <table class="data">
        <thead>
        <tr>
            <th style="width:6%">No</th>
            <th>Nama</th>
            <th style="width:18%">NIP</th>
            <th style="width:16%">Tempat, Tanggal Lahir</th>
            <th style="width:16%">Jabatan</th>
            <th style="width:16%">Unit Kerja</th>
            <th style="width:14%">Tgl Pensiun</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($pegawaiList as $i => $p)
            <tr>
                <td class="tengah">{{ $i + 1 }}</td>
                <td>{{ $p->gelar_depan ? $p->gelar_depan . ' ' : '' }}{{ $p->nama }}{{ $p->gelar ? ', ' . $p->gelar : '' }}</td>
                <td class="mono">{{ $p->nip }}</td>
                <td>{{ $p->tmpt_lahir }}, {{ \Carbon\Carbon::parse($p->tgl_lahir)->translatedFormat('d-m-Y') }}</td>
                <td>{{ $p->jabatan_aktif?->master_jabatan?->nama_jabatan ?? '-' }}</td>
                <td>{{ $p->unit_kerja?->nama_unit ?? '-' }}</td>
                <td class="tengah">{{ \Carbon\Carbon::parse($p->tgl_pensiun)->translatedFormat('d-m-Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="tengah">Tidak ada pegawai yang akan pensiun pada periode ini.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="ttd">
        <div class="tempat-tanggal">{{ $instansi->kabupaten_kota ?? '' }} {{ $instansi->nama_kota_kabupaten ?? '' }}, {{ now()->translatedFormat('d F Y') }}</div>
        <div>Kepala {{ $instansi->nama_instansi_lembaga ?? 'Instansi' }},</div>
        <div class="nama-pejabat">{{ $instansi->kepala_dinas ?? '( ................................ )' }}</div>
        <div class="nip">NIP. {{ $instansi->nip ?? '....................' }}</div>
    </div>

    <div class="footer-info">
        Dicetak dari {{ config('app.name') }} pada {{ now()->translatedFormat('d-m-Y H:i') }}
        oleh {{ auth()->user()->name ?? 'sistem' }}.
    </div>

</body>
</html>
