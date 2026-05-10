<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Cuti - {{ $cuti->no_surat_cuti }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .action-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .btn-print {
            padding: 8px 20px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-family: Arial, sans-serif;
        }
        .btn-back {
            padding: 8px 20px;
            background: #6b7280;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-family: Arial, sans-serif;
        }

        /* Dokumen Surat */
        .surat-doc {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            padding: 20mm 25mm 20mm 30mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* Kop surat */
        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .kop img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .instansi {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text .sub-instansi {
            font-size: 10pt;
            margin-top: 2px;
        }
        .kop-text .alamat {
            font-size: 9pt;
            color: #333;
            margin-top: 2px;
        }

        /* Judul Surat */
        .surat-title {
            text-align: center;
            margin: 16px 0 4px;
        }
        .surat-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .surat-title .surat-number {
            font-size: 11pt;
            margin-top: 4px;
        }
        .surat-title .surat-jenis {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Isi surat */
        .surat-body {
            margin-top: 20px;
            font-size: 11pt;
            line-height: 1.8;
        }
        .surat-body p {
            margin-bottom: 10px;
            text-align: justify;
        }

        /* Tabel data pegawai */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .data-table td {
            vertical-align: top;
            padding: 3px 0;
            font-size: 11pt;
            line-height: 1.6;
        }
        .data-table td:first-child {
            width: 180px;
        }
        .data-table td:nth-child(2) {
            width: 16px;
            text-align: center;
        }

        /* Ketentuan */
        .ketentuan {
            margin-top: 14px;
        }
        .ketentuan ol {
            padding-left: 20px;
        }
        .ketentuan ol li {
            margin-bottom: 6px;
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
        }

        /* Tanda tangan */
        .ttd-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-block {
            text-align: center;
            min-width: 220px;
        }
        .ttd-block .ttd-place-date {
            font-size: 11pt;
            margin-bottom: 4px;
        }
        .ttd-block .ttd-jabatan {
            font-size: 11pt;
            margin-bottom: 60px;
        }
        .ttd-block .ttd-name {
            font-size: 12pt;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
        }
        .ttd-block .ttd-nip {
            font-size: 10pt;
        }

        /* Tembusan */
        .tembusan {
            margin-top: 30px;
            font-size: 10pt;
        }
        .tembusan p { font-weight: bold; margin-bottom: 4px; }
        .tembusan ol { padding-left: 20px; }
        .tembusan ol li { margin-bottom: 2px; }

        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none !important; }
            .surat-doc { box-shadow: none; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <button class="btn-print" onclick="window.print()">🖨 Cetak / Download</button>
        <button class="btn-back" onclick="window.history.back()">← Kembali</button>
    </div>

    <div class="surat-doc">

        {{-- Kop Surat --}}
        <div class="kop">
            @if ($instansi && $instansi->gambar_logo)
                <img src="{{ asset($instansi->gambar_logo) }}" alt="Logo">
            @endif
            <div class="kop-text">
                <div class="instansi">{{ $instansi->nama_instansi_lembaga ?? 'PEMERINTAH DAERAH' }}</div>
                @if ($instansi)
                    <div class="sub-instansi">
                        {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
                    </div>
                    <div class="alamat">{{ $instansi->alamat }} &nbsp;|&nbsp; Telp. {{ $instansi->no_telp }}</div>
                @endif
            </div>
            @if ($instansi && $instansi->gambar_logo)
                <img src="{{ asset($instansi->gambar_logo) }}" alt="Logo">
            @endif
        </div>

        {{-- Judul Surat --}}
        <div class="surat-title">
            <h2>Surat Izin Cuti</h2>
            <div class="surat-jenis">{{ $cuti->jenis_cuti }}</div>
            <div class="surat-number">Nomor: {{ $cuti->no_surat_cuti }}</div>
        </div>

        {{-- Isi Surat --}}
        <div class="surat-body">

            <p>
                Yang bertanda tangan di bawah ini, {{ $instansi->kepala_dinas ?? 'Kepala Dinas' }}
                {{ $instansi->nama_instansi_lembaga ?? '' }}, dengan ini memberikan izin cuti kepada
                Pegawai Negeri Sipil yang namanya tersebut di bawah ini:
            </p>

            {{-- Data Pegawai --}}
            <table class="data-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><strong>{{ $cuti->pegawai->nama ?? '-' }}{{ $cuti->pegawai->gelar ? ', ' . $cuti->pegawai->gelar : '' }}</strong></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>:</td>
                    <td>{{ $cuti->pegawai->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $cuti->pegawai->jabatan_aktif->master_jabatan->nama_jabatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Unit Kerja</td>
                    <td>:</td>
                    <td>{{ $cuti->pegawai->unit_kerja->nama_unit ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Pangkat / Golongan</td>
                    <td>:</td>
                    <td>
                        @if ($pangkat)
                            {{ $pangkat->master_pangkat->nama_pangkat ?? '-' }} /
                            {{ $pangkat->master_golongan->nama_golongan ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            <p style="margin-top: 14px;">
                Untuk melaksanakan <strong>{{ $cuti->jenis_cuti }}</strong> selama
                <strong>{{ $cuti->durasi_cuti }}</strong>, terhitung mulai tanggal
                <strong>{{ \Carbon\Carbon::parse($cuti->pelaksanaan_cuti_mulai)->translatedFormat('d F Y') }}</strong>
                sampai dengan tanggal
                <strong>{{ \Carbon\Carbon::parse($cuti->pelaksanaan_cuti_selesai)->translatedFormat('d F Y') }}</strong>.
            </p>

            {{-- Ketentuan --}}
            <div class="ketentuan">
                <p style="margin-bottom: 6px;">Dengan ketentuan sebagai berikut:</p>
                <ol type="a">
                    <li>{{ $cuti->ketentuan_a }}</li>
                    <li>{{ $cuti->ketentuan_b }}</li>
                    <li>{{ $cuti->ketentuan_c }}</li>
                </ol>
            </div>

            <p style="margin-top: 14px;">
                Demikian surat izin cuti ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
            </p>

        </div>

        {{-- Tanda Tangan --}}
        <div class="ttd-section">
            <div class="ttd-block">
                <div class="ttd-place-date">
                    {{ $instansi->nama_kota_kabupaten ?? '' }},
                    {{ \Carbon\Carbon::parse($cuti->tgl_surat_cuti)->translatedFormat('d F Y') }}
                </div>
                <div class="ttd-jabatan">{{ $instansi->kepala_dinas ?? 'Kepala Dinas' }}</div>
                <div class="ttd-name">{{ $instansi->kepala_dinas ?? '-' }}</div>
                <div class="ttd-nip">NIP. {{ $instansi->nip ?? '-' }}</div>
            </div>
        </div>

        {{-- Tembusan --}}
        <div class="tembusan">
            <p>Tembusan:</p>
            <ol>
                @foreach (explode(',', $cuti->tebusan) as $t)
                    <li>{{ trim($t) }}</li>
                @endforeach
            </ol>
        </div>

    </div>

</body>
</html>
