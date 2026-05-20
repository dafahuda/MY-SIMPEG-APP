<?php

namespace App\Support;

use App\Models\Jabatan;
use App\Models\Pangkat;
use App\Models\Pegawai;

final class ProfilePegawaiUi
{
    public const FALLBACK_LABEL = 'Belum ada data';

    private const TAB_ALIASES = [
        'artis' => 'diklat',
        'diklat' => 'diklat',
    ];

    private const VALID_TABS = [
        'profil',
        'pendidikan',
        'suami_istri',
        'anak',
        'ortu',
        'kgb',
        'skp',
        'tpp',
        'diklat',
    ];

    public static function resolveTab(?string $requestedTab): string
    {
        if ($requestedTab === null || $requestedTab === '') {
            return 'profil';
        }

        if (array_key_exists($requestedTab, self::TAB_ALIASES)) {
            return self::TAB_ALIASES[$requestedTab];
        }

        return in_array($requestedTab, self::VALID_TABS, true) ? $requestedTab : 'profil';
    }

    public static function tabs(): array
    {
        return [
            ['group' => 'Ringkasan', 'items' => [
                ['id' => 'profil', 'label' => 'Profil', 'color' => 'bg-indigo-500'],
            ]],
            ['group' => 'Data Pribadi', 'items' => [
                ['id' => 'pendidikan', 'label' => 'Pendidikan', 'color' => 'bg-indigo-500'],
                ['id' => 'suami_istri', 'label' => 'Suami/Istri', 'color' => 'bg-green-500'],
                ['id' => 'anak', 'label' => 'Anak', 'color' => 'bg-teal-500'],
                ['id' => 'ortu', 'label' => 'Orang Tua', 'color' => 'bg-cyan-500'],
            ]],
            ['group' => 'Riwayat Kepegawaian', 'items' => [
                ['id' => 'kgb', 'label' => 'Pangkat/KGB', 'color' => 'bg-rose-500'],
                ['id' => 'skp', 'label' => 'SKP', 'color' => 'bg-purple-500'],
                ['id' => 'tpp', 'label' => 'TPP', 'color' => 'bg-pink-500'],
                ['id' => 'diklat', 'label' => 'Diklat', 'color' => 'bg-orange-500'],
            ]],
        ];
    }

    public static function toneClasses(): array
    {
        return [
            'indigo' => 'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-900/60 dark:bg-indigo-900/20 dark:text-indigo-200',
            'amber' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/60 dark:bg-amber-900/20 dark:text-amber-200',
            'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/20 dark:text-emerald-200',
            'sky' => 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900/60 dark:bg-sky-900/20 dark:text-sky-200',
            'rose' => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-900/20 dark:text-rose-200',
            'violet' => 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900/60 dark:bg-violet-900/20 dark:text-violet-200',
            'orange' => 'border-orange-200 bg-orange-50 text-orange-700 dark:border-orange-900/60 dark:bg-orange-900/20 dark:text-orange-200',
            'fuchsia' => 'border-fuchsia-200 bg-fuchsia-50 text-fuchsia-700 dark:border-fuchsia-900/60 dark:bg-fuchsia-900/20 dark:text-fuchsia-200',
            'cyan' => 'border-cyan-200 bg-cyan-50 text-cyan-700 dark:border-cyan-900/60 dark:bg-cyan-900/20 dark:text-cyan-200',
            'lime' => 'border-lime-200 bg-lime-50 text-lime-700 dark:border-lime-900/60 dark:bg-lime-900/20 dark:text-lime-200',
            'yellow' => 'border-yellow-200 bg-yellow-50 text-yellow-700 dark:border-yellow-900/60 dark:bg-yellow-900/20 dark:text-yellow-200',
            'pink' => 'border-pink-200 bg-pink-50 text-pink-700 dark:border-pink-900/60 dark:bg-pink-900/20 dark:text-pink-200',
        ];
    }

    public static function summaryCards(Pegawai $pegawai, ?Pangkat $pangkat, ?Jabatan $jabatan): array
    {
        $latestPangkatLabel = collect([
            $pangkat?->master_pangkat?->nama_pangkat,
            $pangkat?->master_golongan?->nama_golongan,
        ])->filter()->join(' / ');

        return [
            ['label' => 'Pangkat Terakhir', 'value' => $latestPangkatLabel ?: self::FALLBACK_LABEL, 'tone' => 'indigo'],
            ['label' => 'Jabatan Aktif', 'value' => $jabatan?->master_jabatan?->nama_jabatan ?: self::FALLBACK_LABEL, 'tone' => 'amber'],
            ['label' => 'Unit Kerja', 'value' => $pegawai->unit_kerja?->nama_unit ?: self::FALLBACK_LABEL, 'tone' => 'emerald'],
            ['label' => 'Status Kepegawaian', 'value' => $pegawai->status_kepegawaian ?: self::FALLBACK_LABEL, 'tone' => 'sky'],
        ];
    }

    public static function kepegawaianLinks(array $counts): array
    {
        return [
            ['label' => 'Periode Pangkat', 'count' => $counts['pangkat'] ?? 0, 'tab' => 'kgb', 'tone' => 'indigo'],
            ['label' => 'Jabatan', 'count' => $counts['jabatan'] ?? 0, 'tab' => 'profil', 'tone' => 'amber'],
            ['label' => 'Periode KGB', 'count' => $counts['allPangkat'] ?? 0, 'tab' => 'kgb', 'tone' => 'emerald'],
            ['label' => 'Seminar', 'count' => $counts['seminar'] ?? 0, 'tab' => 'diklat', 'tone' => 'indigo'],
            ['label' => 'Latihan Jabatan', 'count' => $counts['latihanJab'] ?? 0, 'tab' => 'diklat', 'tone' => 'orange'],
            ['label' => 'Diklat', 'count' => $counts['diklat'] ?? 0, 'tab' => 'diklat', 'tone' => 'fuchsia'],
            ['label' => 'Hukuman', 'count' => $counts['hukuman'] ?? 0, 'info' => 'Data hukuman belum ditampilkan di halaman ini.', 'tone' => 'rose'],
            ['label' => 'Penghargaan', 'count' => $counts['penghargaan'] ?? 0, 'info' => 'Data penghargaan belum ditampilkan di halaman ini.', 'tone' => 'violet'],
            ['label' => 'Cuti', 'count' => $counts['cuti'] ?? 0, 'info' => 'Data cuti belum ditampilkan di halaman ini.', 'tone' => 'cyan'],
            ['label' => 'IKI Tunjangan', 'count' => $counts['tunjangan'] ?? 0, 'info' => 'Data tunjangan belum ditampilkan di halaman ini.', 'tone' => 'lime'],
            ['label' => 'Mutasi', 'count' => $counts['mutasi'] ?? 0, 'info' => 'Data mutasi belum ditampilkan di halaman ini.', 'tone' => 'yellow'],
            ['label' => 'Izin Perkawinan', 'count' => $counts['izinKawin'] ?? 0, 'info' => 'Data izin perkawinan belum ditampilkan di halaman ini.', 'tone' => 'pink'],
        ];
    }

    public static function biodataFields(Pegawai $pegawai, mixed $usia): array
    {
        return [
            ['label' => 'NIP', 'value' => $pegawai->nip],
            ['label' => 'Gelar Depan', 'value' => '-'],
            ['label' => 'Gelar Belakang', 'value' => $pegawai->gelar],
            ['label' => 'Tempat Tanggal Lahir', 'value' => trim(collect([$pegawai->tmpt_lahir, $pegawai->tgl_lahir ? \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') : null])->filter()->join(', '))],
            ['label' => 'Umur', 'value' => $usia ? $usia->y . ' Tahun, ' . $usia->m . ' Bulan, ' . $usia->d . ' Hari' : null],
            ['label' => 'Jenis Kelamin', 'value' => $pegawai->jenis_kelamin ? ucfirst($pegawai->jenis_kelamin) : null],
            ['label' => 'Agama', 'value' => $pegawai->agama],
            ['label' => 'Golongan Darah', 'value' => $pegawai->golongan_darah],
            ['label' => 'Status Pernikahan', 'value' => $pegawai->status_pernikahan],
            ['label' => 'NIK', 'value' => $pegawai->nik],
            ['label' => 'No. Telp', 'value' => $pegawai->no_hp],
            ['label' => 'Email', 'value' => $pegawai->email],
            ['label' => 'Email Gov', 'value' => $pegawai->email_gov],
            ['label' => 'Alamat', 'value' => $pegawai->alamat],
            ['label' => 'No. NPWP', 'value' => $pegawai->no_npwp],
            ['label' => 'No. BPJS', 'value' => $pegawai->no_bpjs],
            ['label' => 'Status Kepegawaian', 'value' => $pegawai->status_kepegawaian],
            ['label' => 'Karpeg', 'value' => $pegawai->karpeg],
            ['label' => 'No. SK CPNS', 'value' => $pegawai->no_sk_cpns],
            ['label' => 'TMT CPNS', 'value' => $pegawai->tmt_cpns],
            ['label' => 'No. SK PNS', 'value' => $pegawai->no_sk_pns],
            ['label' => 'TMT PNS', 'value' => $pegawai->tmt_pns],
        ];
    }

    public static function formatValue(mixed $value): mixed
    {
        return filled($value) ? $value : self::FALLBACK_LABEL;
    }
}
