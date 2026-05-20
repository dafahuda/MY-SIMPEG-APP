<div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div class="fixed inset-0 bg-gray-900/30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" aria-hidden="true" x-cloak></div>

    <!-- Sidebar -->
    <div id="sidebar"
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:w-64! shrink-0 bg-white dark:bg-gray-800 p-4 transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : 'rounded-r-2xl shadow-xs' }}"
        :class="sidebarOpen ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'" @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false">

        <!-- Sidebar header -->
        <div class="flex justify-between mb-10 pr-3 sm:px-2">
            <!-- Close button -->
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="sidebarOpen = !sidebarOpen"
                aria-controls="sidebar" :aria-expanded="sidebarOpen">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                </svg>
            </button>
            <!-- Logo -->
            <a class="block" href="/               <svg class="fill-violet-500" xmlns="http://www.w3.org/2000/svg"
                width="32" height="32">
                <path
                    d="M31.956 14.8C31.372 6.92 25.08.628 17.2.044V5.76a9.04 9.04 0 0 0 9.04 9.04h5.716ZM14.8 26.24v5.716C6.92 31.372.63 25.08.044 17.2H5.76a9.04 9.04 0 0 1 9.04 9.04Zm11.44-9.04h5.716c-.584 7.88-6.876 14.172-14.756 14.756V26.24a9.04 9.04 0 0 1 9.04-9.04ZM.044 14.8C.63 6.92 6.92.628 14.8.044V5.76a9.04 9.04 0 0 1-9.04 9.04H.044Z" />
                </svg>
            </a>
        </div>

        <!-- Links -->
        <div class="space-y-8">
            <!-- Pages group -->
            <div>
                <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                    <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6"
                        aria-hidden="true">•••</span>
                    <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Simpeg App</span>
                </h3>
                @if (auth()->user()->role !== 'pegawai')
                    <ul class="mt-3">
                        <!-- Dashboard -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['dashboard'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['dashboard']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['dashboard'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="/dashboard">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['dashboard'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M5.936.278A7.983 7.983 0 0 1 8 0a8 8 0 1 1-8 8c0-.722.104-1.413.278-2.064a1 1 0 1 1 1.932.516A5.99 5.99 0 0 0 2 8a6 6 0 1 0 6-6c-.53 0-1.045.076-1.548.21A1 1 0 1 1 5.936.278Z" />
                                            <path
                                                d="M6.068 7.482A2.003 2.003 0 0 0 8 10a2 2 0 1 0-.518-3.932L3.707 2.293a1 1 0 0 0-1.414 1.414l3.775 3.775Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Dashboard</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <!-- Manajemen Setup -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['manajemen_setup'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['manajemen_setup']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['manajemen_setup'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="/manajemen_setup" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['manajemen_setup'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M9 6.855A3.502 3.502 0 0 0 8 0a3.5 3.5 0 0 0-1 6.855v1.656L5.534 9.65a3.5 3.5 0 1 0 1.229 1.578L8 10.267l1.238.962a3.5 3.5 0 1 0 1.229-1.578L9 8.511V6.855ZM6.5 3.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Zm4.803 8.095c.005-.005.01-.01.013-.016l.012-.016a1.5 1.5 0 1 1-.025.032ZM3.5 11c.474 0 .897.22 1.171.563l.013.016.013.017A1.5 1.5 0 1 1 3.5 11Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Manajemen
                                            Setup</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 @if (in_array(Request::segment(1), ['manajemen_setup'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    @if (in_array(auth()->user()->role, ['superadmin']))
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('manajemen_setup/instansi_lembaga')) {{ 'text-violet-500!' }} @endif"
                                                href="/manajemen_setup/instansi_lembaga">
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Instansi
                                                    / Lembaga</span>
                                            </a>
                                        </li>
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('manajemen_setup/sekretariat')) {{ 'text-violet-500!' }} @endif"
                                                href="/manajemen_setup/sekretariat">
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Sekretariat</span>
                                            </a>
                                        </li>
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('manajemen_setup/opd_skpd_unitkerja')) {{ 'text-violet-500!' }} @endif"
                                                href="/manajemen_setup/opd_skpd_unitkerja">
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">OPD
                                                    / SKPD / Unit Kerja</span>
                                            </a>
                                        </li>
                                        <li class="mb-1 last:mb-0">
                                            <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('manajemen_setup/data_user_admin')) {{ 'text-violet-500!' }} @endif"
                                                href="/manajemen_setup/data_user_admin">
                                                <span
                                                    class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">User
                                                    Admin</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('manajemen_setup/data_user_pegawai')) {{ 'text-violet-500!' }} @endif"
                                            href="/manajemen_setup/data_user_pegawai">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">User
                                                Pegawai</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (Request::is('data_pegawai/pegawai*')) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ Request::is('data_pegawai/pegawai*') ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!Request::is('data_pegawai/pegawai*')) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="/data_pegawai/pegawai">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['data_pegawai'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M9 6.855A3.502 3.502 0 0 0 8 0a3.5 3.5 0 0 0-1 6.855v1.656L5.534 9.65a3.5 3.5 0 1 0 1.229 1.578L8 10.267l1.238.962a3.5 3.5 0 1 0 1.229-1.578L9 8.511V6.855ZM6.5 3.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Zm4.803 8.095c.005-.005.01-.01.013-.016l.012-.016a1.5 1.5 0 1 1-.025.032ZM3.5 11c.474 0 .897.22 1.171.563l.013.016.013.017A1.5 1.5 0 1 1 3.5 11Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Data
                                            Pegawai</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <!-- Riwayat Keluarga -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['riwayat_keluarga'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['riwayat_keluarga']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['riwayat_keluarga'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" role="button" :aria-expanded="open ? 'true' : 'false'" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['riwayat_keluarga'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M12 1a1 1 0 1 0-2 0v2a3 3 0 0 0 3 3h2a1 1 0 1 0 0-2h-2a1 1 0 0 1-1-1V1ZM1 10a1 1 0 1 0 0 2h2a1 1 0 0 1 1 1v2a1 1 0 1 0 2 0v-2a3 3 0 0 0-3-3H1ZM5 0a1 1 0 0 1 1 1v2a3 3 0 0 1-3 3H1a1 1 0 0 1 0-2h2a1 1 0 0 0 1-1V1a1 1 0 0 1 1-1ZM12 13a1 1 0 0 1 1-1h2a1 1 0 1 0 0-2h-2a3 3 0 0 0-3 3v2a1 1 0 1 0 2 0v-2Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Riwayat
                                            Keluarga</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 @if (in_array(Request::segment(1), ['riwayat_keluarga'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('riwayat_keluarga/suami_istri')) {{ 'text-violet-500!' }} @endif"
                                            href="/riwayat_keluarga/suami_istri">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Suami
                                                / Istri</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('riwayat_keluarga/anak')) {{ 'text-violet-500!' }} @endif"
                                            href="/riwayat_keluarga/anak">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Anak</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('riwayat_keluarga/orang_tua')) {{ 'text-violet-500!' }} @endif"
                                            href="/riwayat_keluarga/orang_tua">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Orang
                                                Tua</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- Riwayat Pendidikan -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['riwayat_pendidikan'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['riwayat_pendidikan']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['riwayat_pendidikan'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="/riwayat_pendidikan" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['riwayat_pendidikan'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M6 0a6 6 0 0 0-6 6c0 1.077.304 2.062.78 2.912a1 1 0 1 0 1.745-.976A3.945 3.945 0 0 1 2 6a4 4 0 0 1 4-4c.693 0 1.344.194 1.936.525A1 1 0 1 0 8.912.779 5.944 5.944 0 0 0 6 0Z" />
                                            <path
                                                d="M10 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm-4 6a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Riwayat
                                            Pendidikan</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 @if (in_array(Request::segment(1), ['riwayat_pendidikan'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('riwayat_pendidikan/sekolah')) {{ 'text-violet-500!' }} @endif"
                                            href="/riwayat_pendidikan/sekolah">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Sekolah</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('riwayat_pendidikan/sekolah_lanjut*')) {{ 'text-violet-500!' }} @endif"
                                            href="/riwayat_pendidikan/sekolah_lanjut">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Sekolah
                                                Lanjut</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('riwayat_pendidikan/pendidikan_bahasa')) {{ 'text-violet-500!' }} @endif"
                                            href="/riwayat_pendidikan/pendidikan_bahasa">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Bahasa</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Kepegawaian -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['kepegawaian'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['kepegawaian']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['kepegawaian'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" role="button" :aria-expanded="open ? 'true' : 'false'" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['kepegawaian'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M6 0a6 6 0 0 0-6 6c0 1.077.304 2.062.78 2.912a1 1 0 1 0 1.745-.976A3.945 3.945 0 0 1 2 6a4 4 0 0 1 4-4c.693 0 1.344.194 1.936.525A1 1 0 1 0 8.912.779 5.944 5.944 0 0 0 6 0Z" />
                                            <path
                                                d="M10 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm-4 6a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Kepegawaian</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 @if (in_array(Request::segment(1), ['kepegawaian'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/jabatan')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/jabatan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Jabatan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/pangkat')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/pangkat">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pangkat</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/hukuman')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/hukuman">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Hukuman</span>
                                        </a>
                                    </li>
                                    <li class="mb-2 mt-2 first:mt-0">
                                        <div
                                            class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                            Alur Diklat
                                        </div>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a data-testid="sidebar-rencana-diklat-link"
                                            class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::routeIs('rencana_diklat.*')) {{ 'text-violet-500!' }} @endif"
                                            href="{{ route('rencana_diklat.index') }}">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Rencana Diklat</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a data-testid="sidebar-diklat-verifikasi-link"
                                            class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::routeIs('diklat_verifikasi.*')) {{ 'text-violet-500!' }} @endif"
                                            href="{{ route('diklat_verifikasi.index') }}">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Verifikasi Pengajuan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/diklat')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/diklat">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Data Diklat Resmi</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a data-testid="sidebar-report-diklat-gap-link"
                                            class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::routeIs('report.diklat_gap')) {{ 'text-violet-500!' }} @endif"
                                            href="{{ route('report.diklat_gap') }}">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Laporan Kesenjangan Diklat Pegawai</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a data-testid="sidebar-report-diklat-gap-unit-link"
                                            class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::routeIs('report.diklat_gap.unit')) {{ 'text-violet-500!' }} @endif"
                                            href="{{ route('report.diklat_gap.unit') }}">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Laporan Kesenjangan Diklat Unit</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/penghargaan')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/penghargaan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Penghargaan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/penugasan_ln')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/penugasan_ln">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Penugasan
                                                LN</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/seminar')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/seminar">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Seminar</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/cuti')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/cuti">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Cuti</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/latihan_jabatan')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/latihan_jabatan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Latihan
                                                Jabatan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/mutasi')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/mutasi">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Mutasi</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/tunjangan')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/tunjangan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Tunjangan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/izin_kawin')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/izin_kawin">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Izin
                                                Kawin</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (Request::is('skp_prestasi_kerja*')) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ Request::is('skp_prestasi_kerja*') ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!Request::is('skp_prestasi_kerja*')) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="/skp_prestasi_kerja/data_prestasi_kerja">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (Request::is('skp_prestasi_kerja*')) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M9 6.855A3.502 3.502 0 0 0 8 0a3.5 3.5 0 0 0-1 6.855v1.656L5.534 9.65a3.5 3.5 0 1 0 1.229 1.578L8 10.267l1.238.962a3.5 3.5 0 1 0 1.229-1.578L9 8.511V6.855ZM6.5 3.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Zm4.803 8.095c.005-.005.01-.01.013-.016l.012-.016a1.5 1.5 0 1 1-.025.032ZM3.5 11c.474 0 .897.22 1.171.563l.013.016.013.017A1.5 1.5 0 1 1 3.5 11Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">SKP
                                            - Prestasi Kerja</span>
                                    </div>
                                </div>
                            </a>
                        </li>

                        <!-- TPP -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['tpp'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['tpp']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['tpp'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" role="button" :aria-expanded="open ? 'true' : 'false'" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['tpp'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M6 0a6 6 0 0 0-6 6c0 1.077.304 2.062.78 2.912a1 1 0 1 0 1.745-.976A3.945 3.945 0 0 1 2 6a4 4 0 0 1 4-4c.693 0 1.344.194 1.936.525A1 1 0 1 0 8.912.779 5.944 5.944 0 0 0 6 0Z" />
                                            <path
                                                d="M10 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm-4 6a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">TPP</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 @if (in_array(Request::segment(1), ['tpp'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('tpp/input_tpp*')) {{ 'text-violet-500!' }} @endif"
                                            href="/tpp/input_tpp">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Input
                                                TPP</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('tpp/laporan_bulanan*')) {{ 'text-violet-500!' }} @endif"
                                            href="/tpp/laporan_bulanan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Laporan
                                                Bulanan</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Notifikasi KGB !-->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (Request::is('notifikasi_kgb*')) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ Request::is('notifikasi_kgb*') ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!Request::is('notifikasi_kgb*')) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="/notifikasi_kgb/data_notifikasi_kgb">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (Request::is('notifikasi_kgb*')) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M9 6.855A3.502 3.502 0 0 0 8 0a3.5 3.5 0 0 0-1 6.855v1.656L5.534 9.65a3.5 3.5 0 1 0 1.229 1.578L8 10.267l1.238.962a3.5 3.5 0 1 0 1.229-1.578L9 8.511V6.855ZM6.5 3.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Zm4.803 8.095c.005-.005.01-.01.013-.016l.012-.016a1.5 1.5 0 1 1-.025.032ZM3.5 11c.474 0 .897.22 1.171.563l.013.016.013.017A1.5 1.5 0 1 1 3.5 11Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Notifikasi
                                            KGB</span>
                                    </div>
                                </div>
                            </a>
                        </li>

                        <!-- Rekapitulasi -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['rekapitulasi'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['rekapitulasi']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['rekapitulasi'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" role="button" :aria-expanded="open ? 'true' : 'false'" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['rekapitulasi'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M6 0a6 6 0 0 0-6 6c0 1.077.304 2.062.78 2.912a1 1 0 1 0 1.745-.976A3.945 3.945 0 0 1 2 6a4 4 0 0 1 4-4c.693 0 1.344.194 1.936.525A1 1 0 1 0 8.912.779 5.944 5.944 0 0 0 6 0Z" />
                                            <path
                                                d="M10 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm-4 6a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Rekapitulasi</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 @if (in_array(Request::segment(1), ['rekapitulasi'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/opd_skpd_unit_kerja')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/opd_skpd_unit_kerja">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                OPD / SKPD / Unit Kerja</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/golongan')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/golongan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Golongan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/pangkat')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/pangkat">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pangkat</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/jabatan')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/jabatan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Jabatan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/eselon')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/eselon">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Eselon</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/status_kepegawaian')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/status_kepegawaian">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Status
                                                Kepegawaian</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/agama')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/agama">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Agama</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/jenis_kelamin')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/jenis_kelamin">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Jenis
                                                Kelamin</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/status_pernikahan')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/status_pernikahan">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Status
                                                Pernikahan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('rekapitulasi/pendidikan_terakhir')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/pendidikan_terakhir">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pendidikan</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Report -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['report'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['report']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['report'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" role="button" :aria-expanded="open ? 'true' : 'false'" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['report'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M6 0a6 6 0 0 0-6 6c0 1.077.304 2.062.78 2.912a1 1 0 1 0 1.745-.976A3.945 3.945 0 0 1 2 6a4 4 0 0 1 4-4c.693 0 1.344.194 1.936.525A1 1 0 1 0 8.912.779 5.944 5.944 0 0 0 6 0Z" />
                                            <path
                                                d="M10 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm-4 6a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Laporan</span>
                                    </div>
                                    <!-- Icon -->
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500 @if (in_array(Request::segment(1), ['report'])) {{ 'rotate-180' }} @endif"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('report/nominatif')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/nominatif">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Nominatif</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('report/duk')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/duk">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">DUK</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('report/bezetting')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/bezetting">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Bezetting</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('report/keadaan_pegawai')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/keadaan_pegawai">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Keadaan
                                                Pegawai</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('report/pensiun')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/pensiun">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pensiun</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Backup database -->
                        <li
                            class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['backup_data'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['backup_data'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="/backup_data">
                                <div class="flex items-center">
                                    <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['backup_data'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16">
                                        <path d="M8 12l-4.5-4.5 1.06-1.06L7 8.88V1h2v7.88l2.44-2.44L12.5 7.5 8 12z" />
                                        <path d="M2 14h12v-2H2v2z" />
                                    </svg>
                                    <span
                                        class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Backup
                                        Database</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                @endif
            </div>

            {{-- ===== MENU KHUSUS ROLE PEGAWAI ===== --}}
            @if (auth()->user()->role === 'pegawai')
                <div>
                    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                        <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6"
                            aria-hidden="true">•••</span>
                        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Layanan Pegawai</span>
                    </h3>
                    <ul class="mt-3">
                        <!-- Profile Saya -->
                        <li
                            class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['profile_saya'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition hover:text-gray-900 dark:hover:text-white"
                                href="{{ route('profile.pegawai') }}">
                                <div class="flex items-center">
                                    <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['profile_saya'])) {{ 'text-violet-500' }} @else {{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Z" />
                                    </svg>
                                    <span
                                        class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Profile
                                        Saya</span>
                                </div>
                            </a>
                        </li>
                        <!-- Diklat Saya -->
                        <li
                            class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (Request::routeIs('diklat_saya.*')) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif">
                            <a data-testid="sidebar-diklat-saya-link"
                                class="block text-gray-800 dark:text-gray-100 truncate transition hover:text-gray-900 dark:hover:text-white"
                                href="{{ route('diklat_saya.index') }}">
                                <div class="flex items-center">
                                    <svg class="shrink-0 fill-current @if (Request::routeIs('diklat_saya.*')) {{ 'text-violet-500' }} @else {{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12.5a.5.5 0 0 1-.777.416L8 11.101l-5.223 3.815A.5.5 0 0 1 2 14.5V2Zm2-1a1 1 0 0 0-1 1v11.518l4.705-3.438a.5.5 0 0 1 .59 0L13 13.518V2a1 1 0 0 0-1-1H4Z" />
                                    </svg>
                                    <span
                                        class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Diklat Saya</span>
                                </div>
                            </a>
                        </li>
                        <!-- Riwayat Keluarga -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['riwayat_keluarga'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['riwayat_keluarga']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition hover:text-gray-900 dark:hover:text-white"
                                href="#0" role="button" :aria-expanded="open ? 'true' : 'false'" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['riwayat_keluarga'])) {{ 'text-violet-500' }} @else {{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M12 1a1 1 0 1 0-2 0v2a3 3 0 0 0 3 3h2a1 1 0 1 0 0-2h-2a1 1 0 0 1-1-1V1ZM1 10a1 1 0 1 0 0 2h2a1 1 0 0 1 1 1v2a1 1 0 1 0 2 0v-2a3 3 0 0 0-3-3H1ZM5 0a1 1 0 0 1 1 1v2a3 3 0 0 1-3 3H1a1 1 0 0 1 0-2h2a1 1 0 0 0 1-1V1a1 1 0 0 1 1-1ZM12 13a1 1 0 0 1 1-1h2a1 1 0 1 0 0-2h-2a3 3 0 0 0-3 3v2a1 1 0 1 0 2 0v-2Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Riwayat
                                            Keluarga</span>
                                    </div>
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate"
                                            href="/riwayat_keluarga/suami_istri">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Suami
                                                / Istri</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate"
                                            href="/riwayat_keluarga/anak">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Anak</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate"
                                            href="/riwayat_keluarga/orang_tua">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Orang
                                                Tua</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- Riwayat Pendidikan -->
                        <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['riwayat_pendidikan'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['riwayat_pendidikan']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition hover:text-gray-900 dark:hover:text-white"
                                href="#0" role="button" :aria-expanded="open ? 'true' : 'false'" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['riwayat_pendidikan'])) {{ 'text-violet-500' }} @else {{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M6 0a6 6 0 0 0-6 6c0 1.077.304 2.062.78 2.912a1 1 0 1 0 1.745-.976A3.945 3.945 0 0 1 2 6a4 4 0 0 1 4-4c.693 0 1.344.194 1.936.525A1 1 0 1 0 8.912.779 5.944 5.944 0 0 0 6 0Z" />
                                            <path
                                                d="M10 4a6 6 0 1 0 0 12 6 6 0 0 0 0-12Zm-4 6a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Riwayat
                                            Pendidikan</span>
                                    </div>
                                    <div
                                        class="flex shrink-0 ml-2 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500"
                                            :class="open ? 'rotate-180' : 'rotate-0'" viewBox="0 0 12 12">
                                            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="lg:hidden lg:sidebar-expanded:block 2xl:block">
                                <ul class="pl-8 mt-1 overflow-hidden" x-show="open"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2">
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate"
                                            href="/riwayat_pendidikan/sekolah">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Sekolah</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate"
                                            href="/riwayat_pendidikan/sekolah_lanjut">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Sekolah
                                                Lanjut</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="block text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate"
                                            href="/riwayat_pendidikan/pendidikan_bahasa">
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Bahasa</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- SKP Prestasi Kerja -->
                        <li
                            class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['skp_prestasi_kerja'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition hover:text-gray-900 dark:hover:text-white"
                                href="/skp_prestasi_kerja/data_prestasi_kerja">
                                <div class="flex items-center">
                                    <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['skp_prestasi_kerja'])) {{ 'text-violet-500' }} @else {{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M9 6.855A3.502 3.502 0 0 0 8 0a3.5 3.5 0 0 0-1 6.855v1.656L5.534 9.65a3.5 3.5 0 1 0 1.229 1.578L8 10.267l1.238.962a3.5 3.5 0 1 0 1.229-1.578L9 8.511V6.855Z" />
                                    </svg>
                                    <span
                                        class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">SKP
                                        - Prestasi Kerja</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="w-12 pl-4 pr-3 py-2">
                <button
                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 transition-colors"
                    @click="sidebarExpanded = !sidebarExpanded">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 sidebar-expanded:rotate-180"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path
                            d="M15 16a1 1 0 0 1-1-1V1a1 1 0 1 1 2 0v14a1 1 0 0 1-1 1ZM8.586 7H1a1 1 0 1 0 0 2h7.586l-2.793 2.793a1 1 0 1 0 1.414 1.414l4.5-4.5A.997.997 0 0 0 12 8.01M11.924 7.617a.997.997 0 0 0-.217-.324l-4.5-4.5a1 1 0 0 0-1.414 1.414L8.586 7M12 7.99a.996.996 0 0 0-.076-.373Z" />
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
