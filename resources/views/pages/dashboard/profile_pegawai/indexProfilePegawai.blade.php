<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Page Header --}}
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Dashboard</span>
                    <span class="mx-1">/</span>
                    <span>Profile Pegawai</span>
                    @if ($pegawai)
                        <span class="mx-1">/</span>
                        <span>{{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}</span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Profile Pegawai</h1>
                @if ($pegawai)
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">NIP. {{ $pegawai->nip }}</p>
                @endif
            </div>
        </div>

        @if (!$pegawai)
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300 px-5 py-4 rounded-lg">
                Data pegawai belum terhubung ke akun ini. Hubungi administrator.
            </div>
        @else

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif



        {{-- Tab Navigation + Content (satu x-data agar tab sinkron) --}}
        <div x-data="{ tab: @js($initialTab) }">

            {{-- Tab Navigation --}}
            <div class="flex flex-wrap items-end gap-3 mb-4 border-b border-gray-200 dark:border-gray-700">
                @foreach ($profileTabs as $group)
                    <div class="pb-2">
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ $group['group'] }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($group['items'] as $t)
                                <button type="button" @click="tab = '{{ $t['id'] }}'"
                                    :class="tab === '{{ $t['id'] }}' ? '{{ $t['color'] }} text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                    class="px-3 py-1.5 text-xs font-medium rounded-t transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    {{ $t['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Print button + Edit button --}}
                <div class="ml-auto flex gap-2">
                    <a href="{{ route('profile.pegawai.edit') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-medium rounded shadow-sm">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 16 16">
                            <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z"/>
                        </svg>
                        Edit Profile
                    </a>
                    <a href="{{ route('profile.pegawai.print') }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded shadow-sm">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 16 16">
                            <path d="M15 10h-2V2H3v8H1a1 1 0 00-1 1v3a1 1 0 001 1h14a1 1 0 001-1v-3a1 1 0 00-1-1zM4 3h8v7H4V3zm8 10H4v-2h8v2zm2 0h-1v-2H3v2H2v-2h1V9h10v2h1v2z"/>
                        </svg>
                        Print
                    </a>
                </div>
            </div>

            {{-- ===== TAB PROFIL ===== --}}
            <div x-show="tab === 'profil'" class="space-y-4">
                <div class="sr-only" data-testid="profile-active-tab">{{ $initialTab }}</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    @foreach ($summaryCards as $card)
                        <div class="rounded-lg border p-4 {{ $toneClasses[$card['tone']] }}">
                            <p class="text-xs font-semibold uppercase tracking-wide opacity-80">{{ $card['label'] }}</p>
                            <p class="mt-2 text-sm font-semibold leading-6 break-words">{{ $card['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                    {{-- Kolom kiri: foto + biodata --}}
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">

                        <div class="flex flex-col sm:flex-row gap-5">
                            {{-- Foto --}}
                            <div class="flex flex-col items-center gap-2 shrink-0">
                                <div class="w-32 h-36 rounded overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700">
                                    @if ($pegawai->foto)
                                        <img src="{{ asset($pegawai->foto) }}" alt="Foto {{ $pegawai->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-xs text-center text-gray-500 dark:text-gray-400 font-mono">{{ $pegawai->nip }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    # Biodata Pegawai
                                </span>
                            </div>

                            {{-- Biodata --}}
                            <div class="flex-1">
                                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">
                                    {{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}
                                </h2>
                                <table class="w-full text-xs">
                                    @foreach ($biodataFields as $f)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="py-1.5 pr-3 text-gray-500 dark:text-gray-400 w-40 shrink-0">{{ $f['label'] }}</td>
                                            <td class="py-1.5 text-gray-800 dark:text-gray-200">{{ \App\Support\ProfilePegawaiUi::formatValue($f['value']) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom kanan: Kepegawaian quick links --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1 flex items-center justify-between">
                            Data Kepegawaian
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M9 6.855A3.502 3.502 0 0 0 8 0a3.5 3.5 0 0 0-1 6.855v1.656L5.534 9.65a3.5 3.5 0 1 0 1.229 1.578L8 10.267l1.238.962a3.5 3.5 0 1 0 1.229-1.578L9 8.511V6.855Z"/>
                            </svg>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Pilih ringkasan untuk membuka bagian data yang sudah tersedia pada halaman ini.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                            @foreach ($kepegawaianLinks as $kl)
                                @if (!empty($kl['tab']))
                                    <button
                                        type="button"
                                        @click="tab = '{{ $kl['tab'] }}'"
                                        class="w-full rounded-lg border px-3 py-3 text-left transition hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 {{ $toneClasses[$kl['tone']] }}"
                                    >
                                        <span class="flex items-start justify-between gap-3">
                                            <span>
                                                <span class="block text-xs font-semibold uppercase tracking-wide opacity-80">{{ $kl['label'] }}</span>
                                                <span class="mt-1 block text-xs opacity-80">Buka bagian terkait</span>
                                            </span>
                                            <span class="inline-flex min-w-8 items-center justify-center rounded-md bg-white/70 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-900/40 dark:text-gray-100">{{ $kl['count'] }}</span>
                                        </span>
                                    </button>
                                @else
                                    <div class="w-full rounded-lg border px-3 py-3 {{ $toneClasses[$kl['tone']] }}">
                                        <span class="flex items-start justify-between gap-3">
                                            <span>
                                                <span class="block text-xs font-semibold uppercase tracking-wide opacity-80">{{ $kl['label'] }}</span>
                                                <span class="mt-1 block text-xs opacity-80">{{ $kl['info'] }}</span>
                                            </span>
                                            <span class="inline-flex min-w-8 items-center justify-center rounded-md bg-white/70 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-900/40 dark:text-gray-100">{{ $kl['count'] }}</span>
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== TAB SUAMI/ISTRI ===== --}}
            <div x-show="tab === 'suami_istri'" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">Data Suami / Istri</h3>
                @if ($suamiIstri->isEmpty())
                    <p class="text-sm text-gray-400 italic">Belum ada data suami / istri.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">NIK</th>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2 text-left">Tempat, Tgl Lahir</th>
                                    <th class="px-3 py-2 text-left">Pendidikan</th>
                                    <th class="px-3 py-2 text-left">Pekerjaan</th>
                                    <th class="px-3 py-2 text-left">Status Hubungan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($suamiIstri as $i => $s)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2">{{ $s->no_ktp_nik ?? '-' }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $s->nama ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ ($s->tempat_lahir ?? '-') . ', ' . ($s->tgl_lahir ?? '-') }}</td>
                                        <td class="px-3 py-2">{{ $s->pendidikan ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $s->pekerjaan ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $s->status_hubungan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ===== TAB ANAK ===== --}}
            <div x-show="tab === 'anak'" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">Data Anak</h3>
                @if ($anak->isEmpty())
                    <p class="text-sm text-gray-400 italic">Belum ada data anak.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">NIK</th>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2 text-left">Tempat, Tgl Lahir</th>
                                    <th class="px-3 py-2 text-left">Jenis Kelamin</th>
                                    <th class="px-3 py-2 text-left">Pendidikan</th>
                                    <th class="px-3 py-2 text-left">Pekerjaan</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($anak as $i => $a)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2">{{ $a->nik ?? '-' }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $a->nama ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ ($a->tempat_lahir ?? '-') . ', ' . ($a->tgl_lahir ?? '-') }}</td>
                                        <td class="px-3 py-2 capitalize">{{ $a->jenis_kelamin ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $a->pendidikan ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $a->pekerjaan ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $a->status_hubungan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ===== TAB ORANG TUA ===== --}}
            <div x-show="tab === 'ortu'" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">Data Orang Tua</h3>
                @if ($orangTua->isEmpty())
                    <p class="text-sm text-gray-400 italic">Belum ada data orang tua.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">NIK</th>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2 text-left">Tempat, Tgl Lahir</th>
                                    <th class="px-3 py-2 text-left">Jenis Kelamin</th>
                                    <th class="px-3 py-2 text-left">Pendidikan</th>
                                    <th class="px-3 py-2 text-left">Pekerjaan</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($orangTua as $i => $ot)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2">{{ $ot->nik ?? '-' }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $ot->nama ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ ($ot->tempat_lahir ?? '-') . ', ' . ($ot->tgl_lahir ?? '-') }}</td>
                                        <td class="px-3 py-2 capitalize">{{ $ot->jenis_kelamin ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $ot->pendidikan ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $ot->pekerjaan ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $ot->status_pekerjaan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ===== TAB PENDIDIKAN ===== --}}
            <div x-show="tab === 'pendidikan'" class="space-y-4">
                {{-- Sekolah --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3">Pendidikan Sekolah</h3>
                    @if ($pendidikanSekolah->isEmpty())
                        <p class="text-sm text-gray-400">Belum ada data.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">No</th>
                                        <th class="px-3 py-2 text-left">Jenjang</th>
                                        <th class="px-3 py-2 text-left">Nama Sekolah</th>
                                        <th class="px-3 py-2 text-left">Jurusan</th>
                                        <th class="px-3 py-2 text-left">Tgl Ijazah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($pendidikanSekolah as $i => $p)
                                        <tr>
                                            <td class="px-3 py-2">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2 font-medium">{{ $p->jenjang_pendidikan }}</td>
                                            <td class="px-3 py-2">{{ $p->nama_sekolah_universitas }}</td>
                                            <td class="px-3 py-2">{{ $p->jurusan ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $p->tgl_ijazah ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                {{-- Lanjut --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3">Pendidikan Lanjut</h3>
                    @if ($pendidikanLanjut->isEmpty())
                        <p class="text-sm text-gray-400">Belum ada data.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">No</th>
                                        <th class="px-3 py-2 text-left">Jenjang</th>
                                        <th class="px-3 py-2 text-left">Universitas</th>
                                        <th class="px-3 py-2 text-left">Jurusan</th>
                                        <th class="px-3 py-2 text-left">Thn Selesai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($pendidikanLanjut as $i => $p)
                                        <tr>
                                            <td class="px-3 py-2">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2 font-medium">{{ $p->jenjang_pendidikan }}</td>
                                            <td class="px-3 py-2">{{ $p->nama_sekolah_universitas }}</td>
                                            <td class="px-3 py-2">{{ $p->jurusan ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $p->thn_selesai ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tab lainnya (SKP, TPP, KGB, Diklat) - placeholder --}}
            <div x-show="tab === 'skp'" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">Data SKP / Prestasi Kerja</h3>
                @if ($skp->isEmpty())
                    <p class="text-sm text-gray-400 italic">Belum ada data SKP / Prestasi Kerja.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">Tahun</th>
                                    <th class="px-3 py-2 text-left">Periode</th>
                                    <th class="px-3 py-2 text-left">Nilai SKP</th>
                                    <th class="px-3 py-2 text-left">Total Nilai</th>
                                    <th class="px-3 py-2 text-left">Pejabat Penilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($skp as $i => $s)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 font-medium">{{ $s->tahun_periode ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ ($s->periode_nilai_dari ?? '-') . ' s/d ' . ($s->periode_nilai_sampai ?? '-') }}</td>
                                        <td class="px-3 py-2">{{ $s->skp ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            @if ($s->total_nilai)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    {{ $s->total_nilai >= 91 ? 'bg-green-100 text-green-800' : ($s->total_nilai >= 76 ? 'bg-blue-100 text-blue-800' : ($s->total_nilai >= 61 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                    {{ $s->total_nilai }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">{{ $s->nama_pejabat_nilai ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div x-show="tab === 'tpp'" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">Data TPP (Tambahan Penghasilan Pegawai)</h3>
                @if ($tpp->isEmpty())
                    <p class="text-sm text-gray-400 italic">Belum ada data TPP.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">Tahun</th>
                                    <th class="px-3 py-2 text-left">Periode</th>
                                    <th class="px-3 py-2 text-left">Jml Hari Kerja</th>
                                    <th class="px-3 py-2 text-left">Tidak Masuk</th>
                                    <th class="px-3 py-2 text-left">Nilai Basic TPP</th>
                                    <th class="px-3 py-2 text-left">Pengurangan Produktifitas</th>
                                    <th class="px-3 py-2 text-left">Pengurangan Disiplin</th>
                                    <th class="px-3 py-2 text-left">TPP Diterima</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($tpp as $i => $t)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 font-medium">{{ $t->tahun ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $t->periode ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $t->jml_hari_kerja ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $t->tidak_masuk_kerja ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $t->nilai_basic_tpp ? 'Rp ' . number_format($t->nilai_basic_tpp, 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-red-600 dark:text-red-400">{{ $t->pengurangan_produktifitas ? 'Rp ' . number_format($t->pengurangan_produktifitas, 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 text-red-600 dark:text-red-400">{{ $t->pengurangan_disiplin ? 'Rp ' . number_format($t->pengurangan_disiplin, 0, ',', '.') : '-' }}</td>
                                        <td class="px-3 py-2 font-semibold text-green-700 dark:text-green-400">{{ $t->tpp_diterima ? 'Rp ' . number_format($t->tpp_diterima, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div x-show="tab === 'kgb'" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-4">Riwayat Pangkat / KGB</h3>
                @if ($allPangkat->isEmpty())
                    <p class="text-sm text-gray-400 italic">Belum ada data riwayat pangkat / KGB.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">No</th>
                                    <th class="px-3 py-2 text-left">Pangkat</th>
                                    <th class="px-3 py-2 text-left">Golongan</th>
                                    <th class="px-3 py-2 text-left">Jenis Pangkat</th>
                                    <th class="px-3 py-2 text-left">TMT Mulai</th>
                                    <th class="px-3 py-2 text-left">TMT Selesai</th>
                                    <th class="px-3 py-2 text-left">No. SK</th>
                                    <th class="px-3 py-2 text-left">Tgl SK</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($allPangkat as $i => $p)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $p->master_pangkat->nama_pangkat ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->master_golongan->nama_golongan ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->jenis_pangkat ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->tmt_pangkat_mulai ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->tmt_pangkat_selesai ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->no_sk ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->tgl_sk ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div x-show="tab === 'diklat'" class="space-y-4">
                @if ($initialTab === 'diklat')
                {{-- Diklat Resmi --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">Diklat Resmi</h3>
                        <a href="{{ route('diklat_saya.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                            Kelola di Diklat Saya &rarr;
                        </a>
                    </div>
                    @if ($diklat->isEmpty())
                        <p class="text-sm text-gray-400 italic">Belum ada data diklat resmi.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">No</th>
                                        <th class="px-3 py-2 text-left">Nama Diklat</th>
                                        <th class="px-3 py-2 text-left">Tahun</th>
                                        <th class="px-3 py-2 text-left">No. Sertifikat / STTPP</th>
                                        <th class="px-3 py-2 text-left">Penyelenggara</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 dark:text-gray-300">
                                    @foreach ($diklat as $d)
                                        <tr class="border-b border-gray-200 dark:border-gray-700">
                                            <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                            <td class="px-3 py-2">
                                                {{ $d->nama_diklat }}
                                                @if ($d->rencanaDiklat)
                                                    <span class="text-xs text-gray-500">({{ $d->rencanaDiklat->nama_diklat_rencana }})</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">{{ $d->tahun }}</td>
                                            <td class="px-3 py-2">{{ $d->no_sttpp ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $d->penyelenggara }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                {{-- Seminar --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3">Seminar / Workshop</h3>
                    @if ($seminar->isEmpty())
                        <p class="text-sm text-gray-400 italic">Belum ada data seminar.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">No</th>
                                        <th class="px-3 py-2 text-left">Nama Seminar</th>
                                        <th class="px-3 py-2 text-left">Penyelenggara</th>
                                        <th class="px-3 py-2 text-left">Tempat</th>
                                        <th class="px-3 py-2 text-left">Tgl Seminar</th>
                                        <th class="px-3 py-2 text-left">Tingkat Kegiatan</th>
                                        <th class="px-3 py-2 text-left">Jml Jam</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($seminar as $i => $sm)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                            <td class="px-3 py-2">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $sm->nama_seminar ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $sm->penyelenggara ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $sm->tempat_seminar ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $sm->tgl_seminar ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $sm->tingkat_kegiatan ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $sm->jumlah_jam ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                {{-- Latihan Jabatan --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-3">Latihan Jabatan</h3>
                    @if ($latihanJab->isEmpty())
                        <p class="text-sm text-gray-400 italic">Belum ada data latihan jabatan.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">No</th>
                                        <th class="px-3 py-2 text-left">Nama Pelatih</th>
                                        <th class="px-3 py-2 text-left">Tempat Latihan</th>
                                        <th class="px-3 py-2 text-left">Waktu Latihan</th>
                                        <th class="px-3 py-2 text-left">Tahun</th>
                                        <th class="px-3 py-2 text-left">Jml Jam</th>
                                        <th class="px-3 py-2 text-left">No. Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($latihanJab as $i => $lj)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                            <td class="px-3 py-2">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $lj->nama_pelatih ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $lj->tempat_latihan ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $lj->waktu_latihan ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $lj->tahun_latihan ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $lj->jumlah_jam ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $lj->nomor_sertifikat ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-5">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200 mb-2">Diklat Saya</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Buka halaman Diklat Saya untuk melihat riwayat resmi, seminar, dan latihan jabatan.</p>
                        <a href="{{ route('diklat_saya.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                            Kelola di Diklat Saya &rarr;
                        </a>
                    </div>
                @endif
            </div>

        </div>{{-- end x-data tab --}}
        @endif

    </div>
</x-app-layout>
