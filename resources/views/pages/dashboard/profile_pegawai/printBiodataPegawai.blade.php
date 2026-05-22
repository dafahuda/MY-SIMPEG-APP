<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata Pegawai - {{ $pegawai->nama }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #111; background: #fff; }
        .page { padding: 15mm 20mm; }

        /* Kop */
        .kop { margin-bottom: 12px; }
        .kop .instansi { font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .kop .sub-instansi { font-size: 10px; text-transform: uppercase; }
        .kop-divider { border-top: 2px solid #111; margin-top: 6px; }

        /* Judul */
        .judul { text-align: center; font-size: 12px; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin: 12px 0 16px; }

        /* Section */
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; margin: 14px 0 6px; }

        /* Data pribadi */
        .data-pribadi { display: flex; gap: 16px; }
        .data-pribadi-fields { flex: 1; }
        .data-pribadi-foto { width: 90px; text-align: center; flex-shrink: 0; }
        .data-pribadi-foto img { width: 90px; height: 110px; object-fit: cover; border: 1px solid #999; }
        .data-pribadi-foto .foto-label { font-size: 9px; margin-top: 3px; }

        .field-row { display: flex; margin-bottom: 3px; font-size: 10px; }
        .field-no { width: 22px; flex-shrink: 0; }
        .field-label { width: 140px; flex-shrink: 0; }
        .field-sep { width: 10px; flex-shrink: 0; }
        .field-value { flex: 1; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 9.5px; margin-top: 4px; }
        th, td { padding: 3px 5px; vertical-align: top; }
        thead th { font-weight: bold; border-bottom: 1px solid #111; }
        tbody td { border-bottom: 1px solid #ddd; }
        .col-no { width: 28px; }

        /* Tanda tangan */
        .ttd { margin-top: 24px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; font-size: 10px; }
        .ttd-box .ttd-kota { margin-bottom: 4px; }
        .ttd-box .ttd-jabatan { margin-bottom: 60px; }
        .ttd-box .ttd-nama { font-weight: bold; }
        .ttd-box .ttd-pangkat { }
        .ttd-box .ttd-nip { }

        /* Print controls */
        .no-print { margin-bottom: 14px; display: flex; gap: 8px; }

        @media print {
            .no-print { display: none !important; }
            @page { size: A4 portrait; margin: 15mm 20mm; }
            body { font-size: 10px; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Print controls --}}
    <div class="no-print">
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
        @if ($instansi)
            <div class="instansi">{{ strtoupper($instansi->nama_instansi_lembaga) }}</div>
            <div class="sub-instansi">{{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}</div>
        @else
            <div class="instansi">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA</div>
        @endif
        <div class="kop-divider"></div>
    </div>

    {{-- Judul --}}
    <div class="judul">Biodata Pegawai</div>

    {{-- I. DATA PRIBADI --}}
    <div class="section-title">I. Data Pribadi</div>
    <div class="data-pribadi">
        <div class="data-pribadi-fields">
            @php
                $pangkatNama   = $pangkat && $pangkat->master_pangkat ? $pangkat->master_pangkat->nama_pangkat : '-';
                $golonganNama  = $pangkat && $pangkat->master_golongan ? $pangkat->master_golongan->nama_golongan : '-';
                $jabatanNama   = $jabatan && $jabatan->master_jabatan  ? $jabatan->master_jabatan->nama_jabatan  : '-';
                $unitKerjaNama = $pegawai->unit_kerja ? $pegawai->unit_kerja->nama_unit : '-';

                $fields = [
                    ['NIP',                    $pegawai->nip],
                    ['Nama',                   ($pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '') . $pegawai->nama . ($pegawai->gelar ? ', ' . $pegawai->gelar : '')],
                    ['Tempat, Tanggal Lahir',  ($pegawai->tmpt_lahir ?? '') . ', ' . ($pegawai->tgl_lahir ?? '')],
                    ['Jenis Kelamin',          ucfirst($pegawai->jenis_kelamin ?? '-')],
                    ['Agama',                  $pegawai->agama ?? '-'],
                    ['Golongan Darah',         $pegawai->golongan_darah ?? '-'],
                    ['Status Pernikahan',      $pegawai->status_pernikahan ?? '-'],
                    ['NIK',                    $pegawai->nik ?? '-'],
                    ['No. Telepon',            $pegawai->no_hp ?? '-'],
                    ['Email',                  $pegawai->email ?? '-'],
                    ['Email Gov',              $pegawai->email_gov ?? '-'],
                    ['Alamat',                 $pegawai->alamat ?? '-'],
                    ['No. NPWP',               $pegawai->no_npwp ?? '-'],
                    ['No. BPJS',               $pegawai->no_bpjs ?? '-'],
                    ['Status Kepegawaian',     $pegawai->status_kepegawaian ?? '-'],
                    ['Karpeg',                 $pegawai->karpeg ?? '-'],
                    ['No. SK CPNS',            $pegawai->no_sk_cpns ?? '-'],
                    ['No. SK PNS',             $pegawai->no_sk_pns ?? '-'],
                    ['OPD / SKPD / Unit Kerja',$unitKerjaNama],
                    ['Pangkat / Gol',          $pangkatNama . ' / ' . $golonganNama],
                    ['Jabatan',                $jabatanNama],
                ];
            @endphp
            @foreach ($fields as $i => $f)
                <div class="field-row">
                    <span class="field-no">{{ $i + 1 }}.</span>
                    <span class="field-label">{{ $f[0] }}</span>
                    <span class="field-sep">:</span>
                    <span class="field-value">{{ $f[1] }}</span>
                </div>
            @endforeach
        </div>
        <div class="data-pribadi-foto">
            @if ($pegawai->foto)
                <img src="{{ asset($pegawai->foto) }}" alt="Foto">
            @else
                <div style="width:90px;height:110px;border:1px solid #999;display:flex;align-items:center;justify-content:center;font-size:9px;color:#999;">
                    No Photo
                </div>
            @endif
            <div class="foto-label">Pas Foto</div>
        </div>
    </div>

    {{-- II. RIWAYAT KELUARGA --}}
    <div class="section-title">II. Riwayat Keluarga</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:120px;">Nama</th>
                <th style="min-width:110px;">Tempat, Tanggal Lahir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $noKeluarga = 1; @endphp
            @foreach ($orangTua as $ot)
                <tr>
                    <td>{{ $noKeluarga++ }}.</td>
                    <td>{{ $ot->nama ?? '-' }}</td>
                    <td>{{ ($ot->tempat_lahir ?? '-') . ', ' . ($ot->tgl_lahir ?? '-') }}</td>
                    <td>{{ $ot->status_pekerjaan ?? '-' }}</td>
                </tr>
            @endforeach
            @foreach ($suamiIstri as $si)
                <tr>
                    <td>{{ $noKeluarga++ }}.</td>
                    <td>{{ $si->nama ?? '-' }}</td>
                    <td>{{ ($si->tempat_lahir ?? '-') . ', ' . ($si->tgl_lahir ?? '-') }}</td>
                    <td>{{ $si->status_hubungan ?? '-' }}</td>
                </tr>
            @endforeach
            @foreach ($anak as $ak)
                <tr>
                    <td>{{ $noKeluarga++ }}.</td>
                    <td>{{ $ak->nama ?? '-' }}</td>
                    <td>{{ ($ak->tempat_lahir ?? '-') . ', ' . ($ak->tgl_lahir ?? '-') }}</td>
                    <td>{{ $ak->status_hubungan ?? 'Anak' }}</td>
                </tr>
            @endforeach
            @if ($noKeluarga === 1)
                <tr><td colspan="4" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endif
        </tbody>
    </table>

    {{-- III. PENDIDIKAN --}}
    <div class="section-title">III. Pendidikan</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:100px;">Sekolah / Universitas</th>
                <th style="width:40px;">Tingkat</th>
                <th style="min-width:80px;">Jurusan</th>
                <th style="min-width:90px;">No. Ijazah</th>
                <th style="min-width:90px;">Kepala / Rektor</th>
            </tr>
        </thead>
        <tbody>
            @php $noPend = 1; @endphp
            @foreach ($pendidikanSekolah as $ps)
                <tr>
                    <td>{{ $noPend++ }}.</td>
                    <td>{{ $ps->nama_sekolah_universitas ?? '-' }}</td>
                    <td>{{ $ps->jenjang_pendidikan ?? '-' }}</td>
                    <td>{{ $ps->jurusan ?? '-' }}</td>
                    <td>{{ $ps->no_ijazah ?? '-' }}</td>
                    <td>{{ $ps->nama_kepsek_rektor ?? '-' }}</td>
                </tr>
            @endforeach
            @foreach ($pendidikanLanjut as $pl)
                <tr>
                    <td>{{ $noPend++ }}.</td>
                    <td>{{ $pl->nama_sekolah_universitas ?? '-' }}</td>
                    <td>{{ $pl->jenjang_pendidikan ?? '-' }}</td>
                    <td>{{ $pl->jurusan ?? '-' }}</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            @endforeach
            @if ($noPend === 1)
                <tr><td colspan="6" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endif
        </tbody>
    </table>

    {{-- IV. KECAKAPAN BAHASA --}}
    <div class="section-title">IV. Kecakapan Bahasa</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:80px;">Jenis Bahasa</th>
                <th style="min-width:80px;">Bahasa</th>
                <th>Kemampuan Bicara</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pendidikanBahasa as $i => $pb)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td>{{ $pb->jenis_bahasa ?? '-' }}</td>
                    <td>{{ $pb->bahasa ?? '-' }}</td>
                    <td>{{ $pb->kemampuan_bicara ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- V. RIWAYAT JABATAN --}}
    <div class="section-title">V. Riwayat Jabatan</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:140px;">Jabatan</th>
                <th style="width:80px;">TMT</th>
                <th style="width:90px;">Selesai Tugas</th>
                <th style="width:60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($allJabatan as $i => $jb)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td>{{ $jb->master_jabatan->nama_jabatan ?? '-' }}</td>
                    <td>{{ $jb->tmt_jabatan_mulai ?? '-' }}</td>
                    <td>{{ $jb->tmt_jabatan_selesai ?? '0000-00-00' }}</td>
                    <td>{{ $jb->tmt_jabatan_selesai && $jb->tmt_jabatan_selesai !== '0000-00-00' ? 'Selesai' : 'Aktif' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- VI. RIWAYAT KEPANGKATAN --}}
    <div class="section-title">VI. Riwayat Kepangkatan</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:100px;">Pangkat</th>
                <th style="width:80px;">TMT</th>
                <th style="width:60px;">Golongan</th>
                <th style="width:60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($allPangkat as $i => $pk)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td>{{ $pk->master_pangkat->nama_pangkat ?? '-' }}</td>
                    <td>{{ $pk->tmt_pangkat_mulai ?? '-' }}</td>
                    <td>{{ $pk->master_golongan->nama_golongan ?? '-' }}</td>
                    <td>{{ $pk->tmt_pangkat_selesai && $pk->tmt_pangkat_selesai !== '0000-00-00' ? 'Selesai' : 'Aktif' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- VII. RIWAYAT PENGHARGAAN --}}
    <div class="section-title">VII. Riwayat Penghargaan</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:140px;">Nama Penghargaan</th>
                <th style="width:50px;">Tahun</th>
                <th>Negara / Instansi Pemberi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penghargaan as $i => $ph)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td>{{ $ph->nama_penghargaan ?? '-' }}</td>
                    <td>{{ $ph->tahun ?? '-' }}</td>
                    <td>{{ $ph->instansi_pemberi ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- VIII. RIWAYAT PENUGASAN LN --}}
    <div class="section-title">VIII. Riwayat Penugasan LN</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:90px;">Negara Tujuan</th>
                <th style="width:50px;">Tahun</th>
                <th style="width:90px;">Lama Penugasan</th>
                <th>Alasan Penugasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penugasanLN as $i => $pln)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td>{{ $pln->negara_tujuan ?? '-' }}</td>
                    <td>{{ $pln->tahun ?? '-' }}</td>
                    <td>{{ ($pln->durasi_hari ?? '-') . ' Hari' }}</td>
                    <td>{{ $pln->alasan_penugasan ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- IX. RIWAYAT HUKUMAN --}}
    <div class="section-title">IX. Riwayat Hukuman</div>
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th style="min-width:100px;">Jenis Hukuman</th>
                <th style="width:80px;">No. SK</th>
                <th style="width:80px;">Tgl. SK</th>
                <th style="width:100px;">No. SK Pemulihan</th>
                <th style="width:80px;">Tgl. Pemulihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hukuman as $i => $hk)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td>{{ $hk->jenis_hukuman ?? '-' }}</td>
                    <td>{{ $hk->no_sk ?? '-' }}</td>
                    <td>{{ $hk->tgl_pengesahan_sk ?? '-' }}</td>
                    <td>{{ $hk->no_pemulihan_hukuman ?? '-' }}</td>
                    <td>{{ $hk->tgl_pemulihan_hukuman ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;color:#999;font-style:italic;">-</td></tr>
            @endforelse
        </tbody>
    </table>

    <div data-testid="print-diklat-section" class="print-section">
        <h3>Rencana dan Realisasi Diklat Saya</h3>
        <div>
            <span data-testid="print-diklat-summary-planned" data-value="{{ $diklatPrintSummary['planned_count'] }}">Rencana: {{ $diklatPrintSummary['planned_count'] }}</span>
            <span data-testid="print-diklat-summary-realized" data-value="{{ $diklatPrintSummary['realized_linked_count'] }}">Terealisasi: {{ $diklatPrintSummary['realized_linked_count'] }}</span>
            <span data-testid="print-diklat-summary-not-realized" data-value="{{ $diklatPrintSummary['not_realized_count'] }}">Belum Terealisasi: {{ $diklatPrintSummary['not_realized_count'] }}</span>
            <span data-testid="print-diklat-summary-out-of-plan" data-value="{{ $diklatPrintSummary['out_of_plan_count'] }}">Di Luar Rencana: {{ $diklatPrintSummary['out_of_plan_count'] }}</span>
        </div>
        <table>
            <thead><tr><th>No</th><th>Nama Diklat</th><th>Tahun</th><th>No. Sertifikat / STTPP</th></tr></thead>
            <tbody>
                @foreach ($diklat as $i => $d)
                <tr><td>{{ $i+1 }}</td><td>{{ $d->nama_diklat }}</td><td>{{ $d->tahun }}</td><td>{{ $d->no_sttpp }}</td></tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <thead><tr><th>No</th><th>Nama Rencana Diklat</th><th>Tahun</th><th>Status</th></tr></thead>
            <tbody>
                @foreach ($rencanaDiklat as $i => $r)
                <tr><td>{{ $i+1 }}</td><td>{{ $r->nama_diklat_rencana }}</td><td>{{ $r->tahun_rencana }}</td><td>{{ $r->status }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Tanda Tangan --}}
    <div class="ttd">
        <div class="ttd-box">
            <div class="ttd-kota">
                @if ($instansi)
                    {{ $instansi->nama_kota_kabupaten }}, {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}
                @else
                    {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}
                @endif
            </div>
            <div class="ttd-jabatan">Pegawai Bersangkutan,</div>
            <div class="ttd-nama">
                {{ ($pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '') . $pegawai->nama . ($pegawai->gelar ? ', ' . $pegawai->gelar : '') }}
            </div>
            @if ($pangkat && $pangkat->master_pangkat)
                <div class="ttd-pangkat">{{ $pangkat->master_pangkat->nama_pangkat }}</div>
            @endif
            <div class="ttd-nip">NIP. {{ $pegawai->nip }}</div>
        </div>
    </div>

</div>
</body>
</html>
