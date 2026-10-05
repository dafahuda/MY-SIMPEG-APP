<li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['rekapitulasi'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['rekapitulasi']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['rekapitulasi'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <x-app.sidebar.nav-icon name="chart-bar" class="shrink-0 w-4 h-4 @if (in_array(Request::segment(1), ['rekapitulasi'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif" />
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
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/opd_skpd_unit_kerja')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/opd_skpd_unit_kerja">
                                            <x-app.sidebar.nav-icon name="hierarchy-2" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                OPD / SKPD / Unit Kerja</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/golongan')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/golongan">
                                            <x-app.sidebar.nav-icon name="badge" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Golongan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/pangkat')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/pangkat">
                                            <x-app.sidebar.nav-icon name="award" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pangkat</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/jabatan')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/jabatan">
                                            <x-app.sidebar.nav-icon name="briefcase" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Jabatan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/eselon')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/eselon">
                                            <x-app.sidebar.nav-icon name="id" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Eselon</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/status_kepegawaian')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/status_kepegawaian">
                                            <x-app.sidebar.nav-icon name="user-check" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Status
                                                Kepegawaian</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/agama')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/agama">
                                            <x-app.sidebar.nav-icon name="world" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Agama</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/jenis_kelamin')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/jenis_kelamin">
                                            <x-app.sidebar.nav-icon name="gender-bigender" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Jenis
                                                Kelamin</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/status_pernikahan')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/status_pernikahan">
                                            <x-app.sidebar.nav-icon name="heart" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Status
                                                Pernikahan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('rekapitulasi/pendidikan_akhir')) {{ 'text-violet-500!' }} @endif"
                                            href="/rekapitulasi/pendidikan_terakhir">
                                            <x-app.sidebar.nav-icon name="school" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pendidikan</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
