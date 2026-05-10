<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>KGB</span>
                    <span class="mx-1">/</span>
                    <span>{{ $periode === 'tahun_depan' ? 'Tahun Depan' : 'Bulan ini' }}</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Notifikasi</h1>
            </div>
            @if ($pegawaiKgb->isNotEmpty())
                <div>
                    <a href="{{ route('kgb.export_excel', ['periode' => $periode]) }}"
                        class="btn bg-green-500 hover:bg-green-600 text-white">
                        <svg class="w-4 h-4 fill-current opacity-80 shrink-0" viewBox="0 0 16 16">
                            <path d="M8 12l-4-4h2.5V2h3v6H12L8 12zm-5 2h10v-2H3v2z" />
                        </svg>
                        <span class="ml-2">Export Excel</span>
                    </a>
                </div>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter Periode -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-5">
            <form action="/notifikasi_kgb/data_notifikasi_kgb" method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Pilih Periode
                    </label>
                    <div x-data="{ open: false, selected: '{{ $periode === 'tahun_depan' ? 'Tahun Depan' : 'Tahun Ini' }}' }" class="relative">
                        <button type="button"
                            @click="open = !open"
                            class="flex items-center justify-between w-48 px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span x-text="selected"></span>
                            <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                            class="absolute z-10 mt-1 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow-lg">
                            <button type="button"
                                @click="selected = 'Tahun Ini'; open = false; $refs.periodeInput.value = 'tahun_ini'; $refs.periodeForm.submit()"
                                class="block w-full text-left px-4 py-2 text-sm {{ $periode !== 'tahun_depan' ? 'bg-indigo-500 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                                Tahun Ini
                            </button>
                            <button type="button"
                                @click="selected = 'Tahun Depan'; open = false; $refs.periodeInput.value = 'tahun_depan'; $refs.periodeForm.submit()"
                                class="block w-full text-left px-4 py-2 text-sm {{ $periode === 'tahun_depan' ? 'bg-indigo-500 text-white' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                                Tahun Depan
                            </button>
                        </div>
                        <input type="hidden" name="periode" x-ref="periodeInput" value="{{ $periode }}">
                    </div>
                    <button type="submit" x-ref="periodeForm"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M15.7 14.3l-3.7-3.7C13.2 9.3 14 7.7 14 6c0-4.4-3.6-8-8-8S-2 1.6-2 6s3.6 8 8 8c1.7 0 3.3-.8 4.6-2l3.7 3.7.4.3c.4 0 .7-.1 1-.4.5-.5.5-1.3 0-1.3zM6 12c-3.3 0-6-2.7-6-6s2.7-6 6-6 6 2.7 6 6-2.7 6-6 6z"/>
                        </svg>
                        Get Notif
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div x-data="{ search: '' }" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">

            <!-- Table controls -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 gap-3">
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Show</span>
                    <select class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <label class="text-gray-500 dark:text-gray-400">Search:</label>
                    <input type="text" x-model="search" placeholder="Cari nama atau NIP..."
                        class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 w-48" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-t border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">NIP</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Nama</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">TTL</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">JK</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No. Telp</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Periode</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Action</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($pegawaiKgb as $index => $pegawai)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30"
                                x-show="search === '' ||
                                    '{{ strtolower($pegawai->nama) }}'.includes(search.toLowerCase()) ||
                                    '{{ $pegawai->nip }}'.includes(search)">
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-800 dark:text-gray-100">{{ $loop->iteration }}</div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-800 dark:text-gray-100">{{ $pegawai->nip }}</div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '' }}{{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-700 dark:text-gray-300">
                                        {{ $pegawai->tmpt_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-700 dark:text-gray-300">
                                        {{ $pegawai->jenis_kelamin === 'laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-700 dark:text-gray-300">{{ $pegawai->no_hp }}</div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-700 dark:text-gray-300">{{ $tahunTarget }}-{{ \Carbon\Carbon::parse($pegawai->tmt_pns)->format('m-d') }}</div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center justify-center">
                                        <a href="/notifikasi_kgb/view_buat_kgb?pegawai_id={{ $pegawai->id }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded shadow-sm">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 16 16">
                                                <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"/>
                                            </svg>
                                            Create KGB
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500">
                                    Tidak ada pegawai yang mendapat KGB pada periode
                                    {{ $periode === 'tahun_depan' ? 'Tahun Depan' : 'Tahun Ini' }}
                                    ({{ $tahunTarget }}).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer info -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-t border-gray-200 dark:border-gray-700 gap-3">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing 1 to {{ $pegawaiKgb->count() }} of {{ $pegawaiKgb->count() }} entries
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
