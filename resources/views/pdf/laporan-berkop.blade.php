<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        /* Font default dompdf; serif cocok untuk dokumen resmi */
        body { font-family: "DejaVu Serif", serif; font-size: 11px; color: #000; margin: 30px 25mm 20px 25mm; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        /* ===== Kop surat ===== */
        .kop { border-bottom: 3px double #000; padding-bottom: 4px; margin-bottom: 14px; }
        .kop table { width: 100%; }
        .kop td.logo { width: 70px; text-align: center; }
        .kop .instansi-jenis { font-size: 13px; letter-spacing: 1px; text-transform: uppercase; }
        .kop .instansi-nama { font-size: 16px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; }
        .kop .instansi-alamat { font-size: 10px; }
        .kop .instansi-kontak { font-size: 10px; }

        /* ===== Judul laporan ===== */
        .judul-laporan { text-align: center; margin: 14px 0 16px 0; }
        .judul-laporan h2 { font-size: 13px; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .judul-laporan .subjudul { font-size: 11px; margin-top: 2px; }

        /* ===== Tabel data ===== */
        .data th, .data td { border: 1px solid #000; padding: 4px 6px; font-size: 11px; }
        .data thead th { text-align: center; font-weight: bold; background-color: #eee; }
        .data .angka { text-align: right; }
        .data .tengah { text-align: center; }

        .total-baris td { font-weight: bold; background-color: #eee; }

        /* ===== Tanda tangan ===== */
        .ttd { margin-top: 26px; width: 60%; margin-left: 40%; text-align: left; font-size: 11px; }
        .ttd .tempat-tanggal { margin-bottom: 40px; }
        .ttd .nama-pejabat { font-weight: bold; text-decoration: underline; margin-top: 40px; }
        .ttd .nip { margin-top: 2px; }

        .footer-info { position: fixed; bottom: 12px; left: 25mm; right: 25mm; font-size: 8px; color: #555;
                       border-top: 1px solid #999; padding-top: 3px; }
    </style>
</head>
<body>

    {{-- Kop surat dari data instansi --}}
    <div class="kop">
        <table>
            <tr>
                <td class="logo">
                    @if (!empty($logoPath) && file_exists($logoPath))
                        <img src="{{ $logoPath }}" style="width: 60px;">
                    @else
                        {{-- Lingkaran pengganti logo jika tidak ada --}}
                        <div style="width: 55px; height: 55px; border: 2px solid #000; border-radius: 50%;
                                    font-size: 9px; text-align: center; line-height: 55px;">LOGO</div>
                    @endif
                </td>
                <td style="text-align: center;">
                    <div class="instansi-jenis">{{ $instansi->jenis_instansi ?? 'PEMERINTAH' }}</div>
                    <div class="instansi-nama">{{ $instansi->nama_instansi ?? config('app.name') }}</div>
                    <div class="instansi-alamat">{{ $instansi->alamat ?? '' }}</div>
                    <div class="instansi-kontak">
                        @if(!empty($instansi->email)) {{ $instansi->email }} @endif
                        @if(!empty($instansi->telepon)) · {{ $instansi->telepon }} @endif
                        @if(!empty($instansi->website)) · {{ $instansi->website }} @endif
                    </div>
                </td>
                <td style="width: 70px;"></td>
            </tr>
        </table>
    </div>

    {{-- Judul laporan --}}
    <div class="judul-laporan">
        <h2>{{ $judul }}</h2>
        @if (!empty($subjudul))
            <div class="subjudul">{{ $subjudul }}</div>
        @endif
        @if (!empty($periode))
            <div class="subjudul">Periode: {{ $periode }}</div>
        @endif
    </div>

    {{-- Tabel data: $kolom = [header...], $baris = [[sel...],...] --}}
    <table class="data">
        <thead>
        <tr>
            @foreach ($kolom as $h)
                <th>{{ $h }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @forelse ($baris as $i => $barisData)
            <tr class="{{ !empty($barisData['total']) ? 'total-baris' : '' }}">
                @foreach ($barisData['sel'] as $j => $sel)
                    <td class="{{ $kolom[$j] === 'Jumlah' || $j === count($kolom) - 1 ? 'angka' : ($j === 0 ? 'tengah' : '') }}">{{ $sel }}</td>
                @endforeach
            </tr>
        @empty
            <tr><td colspan="{{ count($kolom) }}" style="text-align:center;">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Tanda tangan pejabat --}}
    <div class="ttd">
        <div class="tempat-tanggal">{{ $instansi->kota ?? '................' }}, {{ now()->translatedFormat('d F Y') }}</div>
        <div>{{ $jabatanTtd ?? 'Pejabat yang berwenang' }},</div>
        <div class="nama-pejabat">{{ $namaTtd ?? '( ................................ )' }}</div>
        <div class="nip">NIP. {{ $nipTtd ?? '....................' }}</div>
    </div>

    <div class="footer-info">
        Dicetak dari {{ config('app.name') }} pada {{ now()->translatedFormat('d-m-Y H:i') }} WIB
        oleh {{ auth()->user()->name ?? 'sistem' }}.
    </div>

</body>
</html>
