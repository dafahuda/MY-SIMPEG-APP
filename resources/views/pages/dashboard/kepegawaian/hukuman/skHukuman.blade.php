<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Hukuman - {{ $hukuman->no_sk }}</title>
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

        /* Dokumen SK */
        .sk-doc {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            padding: 25mm 25mm 20mm 30mm; /* margin kiri lebih lebar untuk jilid */
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
        }

        /* Kop surat */
        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
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

        /* Judul SK */
        .sk-title {
            text-align: center;
            margin: 20px 0 6px;
        }
        .sk-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sk-title .sk-tentang {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .sk-title .sk-number {
            font-size: 11pt;
            margin-top: 4px;
        }

        /* Menimbang / Mengingat */
        .sk-section {
            margin-top: 16px;
        }
        .sk-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .sk-section table td {
            vertical-align: top;
            padding: 3px 0;
            font-size: 11pt;
            line-height: 1.6;
        }
        .sk-section table td:first-child {
            width: 130px;
            font-weight: bold;
        }
        .sk-section table td:nth-child(2) {
            width: 16px;
            text-align: center;
        }

        /* Diktum */
        .diktum {
            margin-top: 16px;
            text-align: center;
        }
        .diktum h3 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 10px;
        }

        .pasal {
            margin-top: 14px;
        }
        .pasal .pasal-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 6px;
        }
        .pasal table {
            width: 100%;
            border-collapse: collapse;
        }
        .pasal table td {
            vertical-align: top;
            padding: 3px 0;
            font-size: 11pt;
            line-height: 1.6;
        }
        .pasal table td:first-child { width: 200px; }
        .pasal table td:nth-child(2) { width: 16px; text-align: center; }

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
            margin-bottom: 60px; /* ruang tanda tangan */
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
            .sk-doc { box-shadow: none; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <button class="btn-print" onclick="window.print()">🖨 Cetak / Download</button>
        <button class="btn-back" onclick="window.history.back()">← Kembali</button>
    </div>

    <div class="sk-doc">

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

        {{-- Judul --}}
        <div class="sk-title">
            <h2>Surat Keputusan</h2>
            <div class="sk-tentang">
                {{ $instansi->kepala_dinas ?? 'Kepala Dinas' }}
            </div>
            <div class="sk-tentang" style="font-size:11pt; margin-top:2px;">
                {{ strtoupper($instansi->nama_instansi_lembaga ?? '') }}
            </div>
            <div class="sk-number">Nomor: {{ $hukuman->no_sk }}</div>
        </div>

        {{-- Tentang --}}
        <div style="text-align:center; margin-top:10px; font-weight:bold; font-size:11pt; text-transform:uppercase;">
            Tentang<br>
            Penjatuhan Hukuman Disiplin {{ $hukuman->tingkat_hukuman }}<br>
            Berupa {{ $hukuman->jenis_hukuman }}
        </div>

        {{-- Menimbang --}}
        <div class="sk-section" style="margin-top:18px;">
            <table>
                <tr>
                    <td>Menimbang</td>
                    <td>:</td>
                    <td>
                        <ol type="a" style="padding-left:16px;">
                            <li>bahwa berdasarkan hasil pemeriksaan, Saudara {{ $hukuman->pegawai->nama ?? '-' }}
                                NIP. {{ $hukuman->pegawai->nip ?? '-' }} terbukti melakukan pelanggaran disiplin
                                berupa: <em>{{ $hukuman->pelanggaran_yg_dilakukan }}</em>;</li>
                            <li>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a, perlu menetapkan
                                Keputusan tentang Penjatuhan Hukuman Disiplin.</li>
                        </ol>
                    </td>
                </tr>
                <tr style="margin-top:8px;">
                    <td style="padding-top:8px;">Mengingat</td>
                    <td style="padding-top:8px;">:</td>
                    <td style="padding-top:8px;">
                        <ol type="1" style="padding-left:16px;">
                            <li>Undang-Undang Nomor 5 Tahun 2014 tentang Aparatur Sipil Negara;</li>
                            <li>Peraturan Pemerintah Nomor 94 Tahun 2021 tentang Disiplin Pegawai Negeri Sipil;</li>
                            <li>Peraturan Kepala BKN Nomor 6 Tahun 2022 tentang Peraturan Pelaksanaan PP No. 94 Tahun 2021.</li>
                        </ol>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Memutuskan --}}
        <div class="diktum">
            <h3>Memutuskan</h3>
        </div>

        {{-- Menetapkan --}}
        <div class="sk-section">
            <table>
                <tr>
                    <td>Menetapkan</td>
                    <td>:</td>
                    <td>Keputusan tentang Penjatuhan Hukuman Disiplin {{ $hukuman->tingkat_hukuman }}
                        berupa {{ $hukuman->jenis_hukuman }} kepada Pegawai Negeri Sipil yang namanya
                        tersebut di bawah ini.</td>
                </tr>
            </table>
        </div>

        {{-- Pasal 1 --}}
        <div class="pasal">
            <div class="pasal-title">Pasal 1</div>
            <table>
                <tr><td>Nama</td><td>:</td><td>{{ $hukuman->pegawai->nama ?? '-' }}{{ $hukuman->pegawai->gelar ? ', ' . $hukuman->pegawai->gelar : '' }}</td></tr>
                <tr><td>NIP</td><td>:</td><td>{{ $hukuman->pegawai->nip ?? '-' }}</td></tr>
                <tr><td>Unit Kerja</td><td>:</td><td>{{ $hukuman->pegawai->unit_kerja->nama_unit ?? '-' }}</td></tr>
                <tr><td>Pelanggaran</td><td>:</td><td>{{ $hukuman->pelanggaran_yg_dilakukan }}</td></tr>
            </table>
        </div>

        {{-- Pasal 2 --}}
        <div class="pasal" style="margin-top:14px;">
            <div class="pasal-title">Pasal 2</div>
            <table>
                <tr>
                    <td>Jenis Hukuman</td><td>:</td>
                    <td>{{ $hukuman->jenis_hukuman }} (Tingkat {{ $hukuman->tingkat_hukuman }})</td>
                </tr>
                <tr>
                    <td>Isi Teguran / Sanksi</td><td>:</td>
                    <td>{{ $hukuman->isi_teguran }}</td>
                </tr>
                <tr>
                    <td>Berlaku Mulai</td><td>:</td>
                    <td>{{ \Carbon\Carbon::parse($hukuman->tmt_hukuman_mulai)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Pemulihan</td><td>:</td>
                    <td>{{ \Carbon\Carbon::parse($hukuman->tmt_hukuman_pemulihan)->translatedFormat('d F Y') }}</td>
                </tr>
            </table>
        </div>

        {{-- Pasal 3 --}}
        <div class="pasal" style="margin-top:14px;">
            <div class="pasal-title">Pasal 3</div>
            <p style="font-size:11pt; line-height:1.6;">
                Keputusan ini mulai berlaku pada tanggal ditetapkan. Apabila di kemudian hari terdapat
                kekeliruan dalam Keputusan ini, akan diadakan perbaikan sebagaimana mestinya.
            </p>
        </div>

        {{-- Tanda Tangan --}}
        <div class="ttd-section">
            <div class="ttd-block">
                <div class="ttd-place-date">
                    Ditetapkan di {{ $instansi->nama_kota_kabupaten ?? '' }}<br>
                    Pada tanggal {{ \Carbon\Carbon::parse($hukuman->tgl_pengesahan_sk)->translatedFormat('d F Y') }}
                </div>
                <div class="ttd-jabatan">{{ $hukuman->pejabat_pengesahan_sk_hukuman }}</div>
                <div class="ttd-name">{{ $instansi->kepala_dinas ?? 'Kepala Dinas' }}</div>
                <div class="ttd-nip">NIP. {{ $instansi->nip ?? '-' }}</div>
            </div>
        </div>

        {{-- Tembusan --}}
        <div class="tembusan">
            <p>Tembusan:</p>
            <ol>
                <li>Kepala BKN / Kantor Regional BKN yang bersangkutan;</li>
                <li>Pejabat Pembina Kepegawaian yang bersangkutan;</li>
                <li>Pegawai yang bersangkutan;</li>
                <li>Arsip.</li>
            </ol>
        </div>

    </div>

</body>
</html>
