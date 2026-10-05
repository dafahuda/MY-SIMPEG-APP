<x-app-layout>
<div class='px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto'>

    {{-- Alert Welcome --}}
    <div class='mb-6 flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg px-4 py-3 text-sm text-green-800 dark:text-green-300' x-data='{ show: true }' x-show='show'>
        <svg class='shrink-0 w-4 h-4 text-green-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd'/></svg>
        <span><strong>Simpeg App</strong> - Aplikasi Sistem Informasi Manajemen Kepegawaian</span>
        <button class='ml-auto text-green-600 hover:text-green-800' @click='show = false'>&times;</button>
    </div>

    {{-- Kartu Statistik --}}
    <div class='grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6'>

        {{-- Pegawai --}}
        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between'>
            <div>
                <p class='text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1'>PEGAWAI</p>
                <p class='text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1'>{{ $totalPegawai }}</p>
                <p class='text-sm text-gray-500 dark:text-gray-400'>Total Data Pegawai</p>
            </div>
            <div class='w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0'>
                <svg class='w-7 h-7 text-blue-500' fill='currentColor' viewBox='0 0 20 20'><path d='M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z'/></svg>
            </div>
        </div>

        {{-- OPD --}}
        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between'>
            <div>
                <p class='text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1'>OPD / UNIT KERJA</p>
                <p class='text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1'>{{ $totalOPD }}</p>
                <p class='text-sm text-gray-500 dark:text-gray-400'>Total Data OPD / SKPD / Unit Kerja</p>
            </div>
            <div class='w-14 h-14 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center shrink-0'>
                <svg class='w-7 h-7 text-pink-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z' clip-rule='evenodd'/></svg>
            </div>
        </div>

        {{-- Diklat --}}
        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between'>
            <div>
                <p class='text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1'>DIKLAT</p>
                <p class='text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1'>{{ $totalDiklat }}</p>
                <p class='text-sm text-gray-500 dark:text-gray-400'>Total Data Diklat</p>
            </div>
            <div class='w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0'>
                <svg class='w-7 h-7 text-amber-500' fill='currentColor' viewBox='0 0 20 20'><path d='M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z'/></svg>
            </div>
        </div>

        {{-- Penghargaan --}}
        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between'>
            <div>
                <p class='text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1'>PENGHARGAAN</p>
                <p class='text-3xl font-bold text-gray-800 dark:text-gray-100 mb-1'>{{ $totalPenghargaan }}</p>
                <p class='text-sm text-gray-500 dark:text-gray-400'>Total Data Penghargaan</p>
            </div>
            <div class='w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0'>
                <svg class='w-7 h-7 text-emerald-500' fill='currentColor' viewBox='0 0 20 20'><path d='M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z'/></svg>
            </div>
        </div>

    </div>

    @php
        $diklatMetrics = [
            'planned' => [
                'testid' => 'dashboard-diklat-planned',
                'label' => 'Rencana Aktif',
                'description' => 'Target diklat aktif tahun berjalan.',
                'value' => $diklatDashboardAnalytics['summary']['planned_count'],
                'icon_bg' => 'bg-sky-100 dark:bg-sky-900/30',
                'icon_text' => 'text-sky-600 dark:text-sky-300',
            ],
            'realized' => [
                'testid' => 'dashboard-diklat-realized',
                'label' => 'Terealisasi',
                'description' => 'Rencana yang sudah terhubung ke realisasi.',
                'value' => $diklatDashboardAnalytics['summary']['realized_linked_count'],
                'icon_bg' => 'bg-green-100 dark:bg-green-900/30',
                'icon_text' => 'text-green-600 dark:text-green-300',
            ],
            'not_realized' => [
                'testid' => 'dashboard-diklat-not-realized',
                'label' => 'Belum Terealisasi',
                'description' => 'Rencana aktif yang belum punya realisasi.',
                'value' => $diklatDashboardAnalytics['summary']['not_realized_count'],
                'icon_bg' => 'bg-yellow-100 dark:bg-yellow-900/30',
                'icon_text' => 'text-yellow-600 dark:text-yellow-300',
            ],
            'out_of_plan' => [
                'testid' => 'dashboard-diklat-out-of-plan',
                'label' => 'Di Luar Rencana',
                'description' => 'Realisasi tanpa rencana terhubung.',
                'value' => $diklatDashboardAnalytics['summary']['out_of_plan_count'],
                'icon_bg' => 'bg-red-100 dark:bg-red-900/30',
                'icon_text' => 'text-red-600 dark:text-red-300',
            ],
        ];
    @endphp

    {{-- Analitik Diklat Tahun Berjalan --}}
    <div class='grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6'>
        <div class='xl:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex flex-col gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between'>
                <div>
                    <div class='flex items-center gap-2'>
                        <svg class='w-4 h-4 text-sky-500' fill='currentColor' viewBox='0 0 20 20'><path d='M10 2a1 1 0 01.894.553l1.382 2.794 3.084.448a1 1 0 01.554 1.706l-2.233 2.177.527 3.071a1 1 0 01-1.451 1.054L10 12.347l-2.757 1.456a1 1 0 01-1.45-1.054l.526-3.071L4.086 7.5a1 1 0 01.555-1.706l3.083-.448 1.382-2.794A1 1 0 0110 2z'/></svg>
                        <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Analitik Diklat Tahun {{ $diklatDashboardAnalytics['year'] }}</span>
                    </div>
                    <p class='mt-1 text-sm text-gray-500 dark:text-gray-400'>Ringkasan plan vs realisasi untuk {{ $diklatDashboardAnalytics['scope_label'] }}.</p>
                </div>
                <span class='inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700/60 px-3 py-1 text-xs font-medium text-gray-600 dark:text-gray-300'>Current year · {{ $diklatDashboardAnalytics['year'] }}</span>
            </div>

            <div class='grid grid-cols-1 gap-4 p-5 sm:grid-cols-2'>
                @foreach ($diklatMetrics as $metric)
                    <div data-testid='{{ $metric['testid'] }}' data-value='{{ $metric['value'] }}' class='rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 p-4'>
                        <div class='flex items-start justify-between gap-3'>
                            <div>
                                <p class='text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide'>{{ $metric['label'] }}</p>
                                <p class='mt-2 text-3xl font-bold text-gray-800 dark:text-gray-100'>{{ number_format($metric['value']) }}</p>
                                <p class='mt-1 text-sm text-gray-500 dark:text-gray-400'>{{ $metric['description'] }}</p>
                            </div>
                            <div class='w-12 h-12 rounded-full {{ $metric['icon_bg'] }} flex items-center justify-center shrink-0'>
                                <svg class='w-6 h-6 {{ $metric['icon_text'] }}' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.536-9.95a1 1 0 10-1.414-1.414L9 9.757 7.879 8.636a1 1 0 10-1.414 1.414l1.828 1.828a1 1 0 001.414 0l3.829-3.828z' clip-rule='evenodd'/></svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
                <svg class='w-4 h-4 text-violet-500' fill='currentColor' viewBox='0 0 20 20'><path d='M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z'/></svg>
                <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Status Plan vs Realisasi</span>
            </div>
            <div class='p-5'>
                <p class='mb-3 text-sm text-gray-500 dark:text-gray-400'>Komposisi status diklat tahun {{ $diklatDashboardAnalytics['year'] }} untuk scope dashboard saat ini.</p>
                <canvas id='chartDiklatStatus' data-testid='dashboard-diklat-status-chart' style='max-height:220px;'></canvas>
            </div>
        </div>
    </div>

    {{-- Chart OPD --}}
    <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6'>
        <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
            <svg class='w-4 h-4 text-amber-500' fill='currentColor' viewBox='0 0 20 20'><path d='M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z'/></svg>
            <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Statistik OPD / SKPD / Unit Kerja</span>
        </div>
        <div class='p-5'>
            <canvas id='chartOPD' style='max-height:320px;'></canvas>
        </div>
    </div>

    {{-- Tabel Berkala Gaji & Pangkat --}}
    <div class='grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6'>

        {{-- Berkala Gaji --}}
        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
                <svg class='w-4 h-4 text-amber-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z' clip-rule='evenodd'/></svg>
                <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Berkala Gaji 1 Bulan Kedepan</span>
            </div>
            <div class='overflow-x-auto'>
                <table class='w-full text-sm'>
                    <thead>
                        <tr class='bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase'>
                            <th class='px-4 py-3 text-left font-semibold'>NIP</th>
                            <th class='px-4 py-3 text-left font-semibold'>Nama</th>
                            <th class='px-4 py-3 text-left font-semibold'>TTL</th>
                            <th class='px-4 py-3 text-left font-semibold'>Periode</th>
                        </tr>
                    </thead>
                    <tbody class='divide-y divide-gray-100 dark:divide-gray-700'>
                        @forelse($gajiMendatang as $item)
                        <tr class='hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors'>
                            <td class='px-4 py-3 text-gray-700 dark:text-gray-300 font-mono text-xs'>{{ $item['nip'] }}</td>
                            <td class='px-4 py-3 text-gray-800 dark:text-gray-200'>{{ $item['nama'] }}</td>
                            <td class='px-4 py-3 text-gray-600 dark:text-gray-400 text-xs'>{{ $item['ttl'] }}</td>
                            <td class='px-4 py-3'><span class='inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'>{{ $item['periode'] }}</span></td>
                        </tr>
                        @empty
                        <x-empty-state colspan="4" icon="default" title="Tidak ada data" message="Tidak ada data" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Berkala Pangkat --}}
        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
                <svg class='w-4 h-4 text-amber-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z' clip-rule='evenodd'/></svg>
                <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Berkala Pangkat 1 Bulan Kedepan</span>
            </div>
            <div class='overflow-x-auto'>
                <table class='w-full text-sm'>
                    <thead>
                        <tr class='bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase'>
                            <th class='px-4 py-3 text-left font-semibold'>NIP</th>
                            <th class='px-4 py-3 text-left font-semibold'>Nama</th>
                            <th class='px-4 py-3 text-left font-semibold'>TTL</th>
                            <th class='px-4 py-3 text-left font-semibold'>Periode</th>
                        </tr>
                    </thead>
                    <tbody class='divide-y divide-gray-100 dark:divide-gray-700'>
                        @forelse($pangkatMendatang as $item)
                        <tr class='hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors'>
                            <td class='px-4 py-3 text-gray-700 dark:text-gray-300 font-mono text-xs'>{{ $item['nip'] }}</td>
                            <td class='px-4 py-3 text-gray-800 dark:text-gray-200'>{{ $item['nama'] }}</td>
                            <td class='px-4 py-3 text-gray-600 dark:text-gray-400 text-xs'>{{ $item['ttl'] }}</td>
                            <td class='px-4 py-3'><span class='inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300'>{{ $item['periode'] }}</span></td>
                        </tr>
                        @empty
                        <x-empty-state colspan="4" icon="default" title="Tidak ada data" message="Tidak ada data" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Chart Golongan & Eselon --}}
    <div class='grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6'>

        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
                <svg class='w-4 h-4 text-amber-500' fill='currentColor' viewBox='0 0 20 20'><path d='M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z'/></svg>
                <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Statistik Golongan</span>
            </div>
            <div class='p-5'>
                <canvas id='chartGolongan' style='max-height:280px;'></canvas>
            </div>
        </div>

        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
                <svg class='w-4 h-4 text-amber-500' fill='currentColor' viewBox='0 0 20 20'><path d='M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z'/></svg>
                <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Statistik Eselon</span>
            </div>
            <div class='p-5'>
                <canvas id='chartEselon' style='max-height:280px;'></canvas>
            </div>
        </div>

    </div>

    {{-- Chart Jenis Kelamin & Status Kepegawaian --}}
    <div class='grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6'>

        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
                <svg class='w-4 h-4 text-cyan-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd'/></svg>
                <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Statistik Jenis Kelamin</span>
            </div>
            <div class='p-5 flex justify-center'>
                <canvas id='chartJenisKelamin' style='max-height:260px;max-width:260px;'></canvas>
            </div>
        </div>

        <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700'>
            <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
                <svg class='w-4 h-4 text-cyan-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd'/></svg>
                <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Statistik Status Kepegawaian</span>
            </div>
            <div class='p-5 flex justify-center'>
                <canvas id='chartStatus' style='max-height:260px;max-width:260px;'></canvas>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const opdLabels = @json(collect($pegawaiPerUnitKerja)->pluck('unit_kerja'));
    const opdData   = @json(collect($pegawaiPerUnitKerja)->pluck('total'));
    const opdColors = ['#3b82f6','#ec4899','#f59e0b','#10b981','#8b5cf6','#ef4444','#06b6d4','#84cc16','#f97316','#6366f1','#14b8a6','#e11d48'];
    new Chart(document.getElementById('chartOPD'), {
        type: 'bar',
        data: {
            labels: opdLabels,
            datasets: [{ label: 'Jumlah Pegawai', data: opdData, backgroundColor: opdColors.slice(0, opdLabels.length), borderRadius: 4 }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Statistik Jumlah Pegawai Berdasarkan OPD / SKPD / Unit Kerja', font: { size: 13 } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, title: { display: true, text: 'Jumlah' } },
                x: { ticks: { maxRotation: 40, minRotation: 30, font: { size: 10 } } }
            }
        }
    });

    const golLabels = @json(collect($statistikGolongan)->pluck('golongan'));
    const golData   = @json(collect($statistikGolongan)->pluck('jumlah'));
    new Chart(document.getElementById('chartGolongan'), {
        type: 'bar',
        data: { labels: golLabels, datasets: [{ label: 'Jumlah', data: golData, backgroundColor: '#3b82f6', borderRadius: 3 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 10 } } } } }
    });

    const eselonLabels = @json(collect($statistikEselon)->pluck('eselon'));
    const eselonData   = @json(collect($statistikEselon)->pluck('jumlah'));
    new Chart(document.getElementById('chartEselon'), {
        type: 'bar',
        data: { labels: eselonLabels, datasets: [{ label: 'Jumlah', data: eselonData, backgroundColor: '#8b5cf6', borderRadius: 3 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 10 } } } } }
    });

    const jkLabels = @json(collect($statistikJenisKelamin)->pluck('jenis'));
    const jkData   = @json(collect($statistikJenisKelamin)->pluck('jumlah'));
    new Chart(document.getElementById('chartJenisKelamin'), {
        type: 'doughnut',
        data: { labels: jkLabels, datasets: [{ data: jkData, backgroundColor: ['#3b82f6','#ec4899'], hoverOffset: 6 }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    const statusLabels = @json(collect($statistikStatus)->pluck('status'));
    const statusData   = @json(collect($statistikStatus)->pluck('jumlah'));
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: ['#10b981','#f59e0b','#6366f1'], hoverOffset: 6 }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    const diklatChartElement = document.getElementById('chartDiklatStatus');
    const diklatChartLabels = @json(collect($diklatDashboardAnalytics['chart'])->pluck('label'));
    const diklatChartData = @json(collect($diklatDashboardAnalytics['chart'])->pluck('value'));
    const chartTheme = getComputedStyle(document.documentElement);
    const diklatChartColors = [
        chartTheme.getPropertyValue('--color-sky-500').trim() || '#67bfff',
        chartTheme.getPropertyValue('--color-green-500').trim() || '#3ec972',
        chartTheme.getPropertyValue('--color-yellow-500').trim() || '#f0bb33',
        chartTheme.getPropertyValue('--color-red-500').trim() || '#ff5656',
    ];

    if (diklatChartElement) {
        new Chart(diklatChartElement, {
            type: 'doughnut',
            data: {
                labels: diklatChartLabels,
                datasets: [{
                    data: diklatChartData,
                    backgroundColor: diklatChartColors,
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            padding: 12,
                            font: { size: 11 },
                        },
                    },
                },
            },
        });
    }

});
</script>
@endpush
</x-app-layout>
