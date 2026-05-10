<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Penghargaan - {{ $penghargaan->no_sertifikat }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            min-height: 100vh;
        }

        /* Tombol aksi (hilang saat print) */
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
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        .btn-back {
            padding: 8px 20px;
            background: #6b7280;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        /* Sertifikat */
        .certificate {
            width: 210mm;
            min-height: 148mm; /* A5 landscape */
            background: #fff;
            border: 12px double #b8860b;
            padding: 30px 40px;
            position: relative;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* Ornamen sudut */
        .certificate::before,
        .certificate::after {
            content: '✦';
            position: absolute;
            font-size: 24px;
            color: #b8860b;
        }
        .certificate::before { top: 8px; left: 12px; }
        .certificate::after  { bottom: 8px; right: 12px; }

        /* Header instansi */
        .header {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 2px solid #b8860b;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }
        .header-text { flex: 1; text-align: center; }
        .header-text .instansi-name {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1a1a1a;
        }
        .header-text .instansi-sub {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }

        /* Judul sertifikat */
        .cert-title {
            text-align: center;
            margin-bottom: 18px;
        }
        .cert-title h1 {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #b8860b;
            text-shadow: 1px 1px 0 rgba(0,0,0,0.1);
        }
        .cert-title .cert-subtitle {
            font-size: 12px;
            color: #555;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        /* Nomor sertifikat */
        .cert-number {
            text-align: center;
            font-size: 11px;
            color: #777;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        /* Body teks */
        .cert-body {
            text-align: center;
            line-height: 1.8;
        }
        .cert-body .given-to {
            font-size: 13px;
            color: #444;
            margin-bottom: 6px;
        }
        .cert-body .recipient-name {
            font-size: 26px;
            font-weight: bold;
            color: #1a1a1a;
            border-bottom: 2px solid #b8860b;
            display: inline-block;
            padding: 0 20px 4px;
            margin-bottom: 8px;
            font-style: italic;
        }
        .cert-body .recipient-nip {
            font-size: 12px;
            color: #555;
            margin-bottom: 12px;
        }
        .cert-body .award-desc {
            font-size: 13px;
            color: #333;
            margin-bottom: 4px;
        }
        .cert-body .award-name {
            font-size: 18px;
            font-weight: bold;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .cert-body .award-level {
            font-size: 12px;
            color: #555;
            margin-bottom: 4px;
        }
        .cert-body .award-place-date {
            font-size: 12px;
            color: #555;
        }

        /* Footer tanda tangan */
        .cert-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 24px;
        }
        .signature-block {
            text-align: center;
            min-width: 180px;
        }
        .signature-block .sign-place-date {
            font-size: 11px;
            color: #444;
            margin-bottom: 50px; /* ruang tanda tangan */
        }
        .signature-block .sign-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a1a;
            border-top: 1px solid #333;
            padding-top: 4px;
        }
        .signature-block .sign-nip {
            font-size: 10px;
            color: #555;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(184, 134, 11, 0.06);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none !important; }
            .certificate { box-shadow: none; border: 12px double #b8860b; }
            @page { size: A5 landscape; margin: 0; }
        }
    </style>
</head>
<body>

    {{-- Tombol aksi --}}
    <div class="action-bar">
        <button class="btn-print" onclick="window.print()">🖨 Cetak / Download</button>
        <button class="btn-back" onclick="window.history.back()">← Kembali</button>
    </div>

    {{-- Sertifikat --}}
    <div class="certificate">

        {{-- Watermark --}}
        <div class="watermark">Penghargaan</div>

        {{-- Header --}}
        <div class="header">
            @if ($instansi && $instansi->gambar_logo)
                <img src="{{ asset($instansi->gambar_logo) }}" alt="Logo">
            @endif
            <div class="header-text">
                <div class="instansi-name">
                    {{ $instansi->nama_instansi_lembaga ?? 'PEMERINTAH DAERAH' }}
                </div>
                @if ($instansi)
                    <div class="instansi-sub">
                        {{ $instansi->kabupaten_kota }} {{ $instansi->nama_kota_kabupaten }}
                        &nbsp;|&nbsp; {{ $instansi->alamat }}
                    </div>
                @endif
            </div>
            @if ($instansi && $instansi->gambar_logo)
                <img src="{{ asset($instansi->gambar_logo) }}" alt="Logo">
            @endif
        </div>

        {{-- Judul --}}
        <div class="cert-title">
            <h1>Sertifikat</h1>
            <div class="cert-subtitle">Penghargaan Pegawai</div>
        </div>

        {{-- Nomor --}}
        <div class="cert-number">
            Nomor: <strong>{{ $penghargaan->no_sertifikat }}</strong>
        </div>

        {{-- Body --}}
        <div class="cert-body">
            <div class="given-to">Diberikan kepada:</div>
            <div class="recipient-name">
                {{ $penghargaan->pegawai->nama ?? '-' }}{{ $penghargaan->pegawai->gelar ? ', ' . $penghargaan->pegawai->gelar : '' }}
            </div>
            <div class="recipient-nip">
                NIP. {{ $penghargaan->pegawai->nip ?? '-' }}
                @if ($penghargaan->pegawai->unit_kerja)
                    &nbsp;|&nbsp; {{ $penghargaan->pegawai->unit_kerja->nama_unit }}
                @endif
            </div>
            <div class="award-desc">Atas prestasi dan dedikasinya sebagai:</div>
            <div class="award-name">{{ $penghargaan->nama_penghargaan }}</div>
            <div class="award-level">
                Tingkat: <strong>{{ $penghargaan->tingkat_kegiatan }}</strong>
                &nbsp;&bull;&nbsp; Tahun: <strong>{{ $penghargaan->tahun }}</strong>
            </div>
            <div class="award-place-date">
                {{ $penghargaan->tempat_penghargaan }},
                {{ \Carbon\Carbon::parse($penghargaan->tgl_penghargaan)->translatedFormat('d F Y') }}
            </div>
        </div>

        {{-- Footer tanda tangan --}}
        <div class="cert-footer">
            <div class="signature-block">
                <div class="sign-place-date">
                    {{ $instansi ? $instansi->nama_kota_kabupaten : '' }},
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>
                <div class="sign-name">{{ $instansi->kepala_dinas ?? 'Kepala Dinas' }}</div>
                <div class="sign-nip">NIP. {{ $instansi->nip ?? '-' }}</div>
            </div>
        </div>

    </div>

</body>
</html>
