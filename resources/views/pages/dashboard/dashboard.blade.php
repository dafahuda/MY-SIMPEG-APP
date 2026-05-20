<x-app-layout>
<div class='px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto'>

    {{-- Page Header --}}
    <div class='sm:flex sm:justify-between sm:items-start mb-8 gap-4'>
        <div class='mb-4 sm:mb-0'>
            <nav class='text-sm text-gray-500 dark:text-gray-400 mb-1'>
                <span>SIMPEG ASN</span>
                <span class='mx-1'>/</span>
                <span>Dashboard</span>
            </nav>
            <h1 class='text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold'>Dashboard Kepegawaian</h1>
            <p class='mt-2 text-sm text-gray-500 dark:text-gray-400'>Ringkasan data ASN, unit kerja, dan agenda kepegawaian resmi.</p>
        </div>
    </div>

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
                        <tr><td colspan='4' class='px-4 py-6 text-center text-gray-400 text-sm'>Tidak ada data</td></tr>
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
                        <tr><td colspan='4' class='px-4 py-6 text-center text-gray-400 text-sm'>Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Analitik Diklat --}}
    <div class='bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6'>
        <div class='flex items-center gap-2 px-5 py-4 border-b border-gray-200 dark:border-gray-700'>
            <svg class='w-4 h-4 text-indigo-500' fill='currentColor' viewBox='0 0 20 20'><path d='M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z'/></svg>
            <span class='font-semibold text-gray-700 dark:text-gray-200 text-sm'>Analitik Diklat Tahun {{ $diklatAnalytics['year'] }}</span>
        </div>
        <div class='p-5'>
            <p class='text-sm text-gray-600 dark:text-gray-400 mb-4'>Ringkasan plan vs realisasi untuk {{ $diklatAnalytics['unit_name'] }}.</p>
            <div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4'>
                <div class='bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4'>
                    <p class='text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1'>Direncanakan</p>
                    <p class='text-2xl font-bold text-blue-700 dark:text-blue-300' data-testid='dashboard-diklat-planned' data-value='{{ $diklatAnalytics['planned_count'] }}'>{{ $diklatAnalytics['planned_count'] }}</p>
                </div>
                <div class='bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4'>
                    <p class='text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide mb-1'>Terealisasi</p>
                    <p class='text-2xl font-bold text-green-700 dark:text-green-300' data-testid='dashboard-diklat-realized' data-value='{{ $diklatAnalytics['realized_linked_count'] }}'>{{ $diklatAnalytics['realized_linked_count'] }}</p>
                </div>
                <div class='bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4'>
                    <p class='text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide mb-1'>Belum Terealisasi</p>
                    <p class='text-2xl font-bold text-amber-700 dark:text-amber-300' data-testid='dashboard-diklat-not-realized' data-value='{{ $diklatAnalytics['not_realized_count'] }}'>{{ $diklatAnalytics['not_realized_count'] }}</p>
                </div>
                <div class='bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg p-4'>
                    <p class='text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1'>Di Luar Rencana</p>
                    <p class='text-2xl font-bold text-purple-700 dark:text-purple-300' data-testid='dashboard-diklat-out-of-plan' data-value='{{ $diklatAnalytics['out_of_plan_count'] }}'>{{ $diklatAnalytics['out_of_plan_count'] }}</p>
                </div>
            </div>
            <div data-testid='dashboard-diklat-status-chart' class='flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 px-4 py-3 text-sm text-gray-500 dark:text-gray-400'>
                <svg class='mt-0.5 w-4 h-4 shrink-0 text-gray-400 dark:text-gray-500' fill='currentColor' viewBox='0 0 20 20'><path fill-rule='evenodd' d='M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z' clip-rule='evenodd'/></svg>
                <span>Visualisasi grafik status diklat belum tersedia. Gunakan <a href="{{ route('report.diklat_gap') }}" class='underline hover:text-indigo-600 dark:hover:text-indigo-400'>Laporan Gap Diklat</a> untuk analisis lengkap.</span>
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

});
</script>
@endpush
</x-app-layout>