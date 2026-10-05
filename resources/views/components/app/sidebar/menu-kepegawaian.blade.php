<li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['kepegawaian'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['kepegawaian']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['kepegawaian'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <x-app.sidebar.nav-icon name="briefcase" class="shrink-0 w-4 h-4 @if (in_array(Request::segment(1), ['kepegawaian'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif" />
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
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/jabatan')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/jabatan">
                                            <x-app.sidebar.nav-icon name="id" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Jabatan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/pangkat')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/pangkat">
                                            <x-app.sidebar.nav-icon name="badge" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pangkat</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/hukuman')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/hukuman">
                                            <x-app.sidebar.nav-icon name="gavel" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Hukuman</span>
                                        </a>
                                    </li>
                                     <li class="mb-1 last:mb-0">
                                         <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/diklat')) {{ 'text-violet-500!' }} @endif"
                                             href="/kepegawaian/diklat">
                                             <x-app.sidebar.nav-icon name="school" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                 class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Diklat</span>
                                         </a>
                                     </li>
                                     <li class="mb-1 last:mb-0">
                                         <a data-testid="sidebar-rencana-diklat-link"
                                             class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Request::is('kepegawaian/rencana_diklat*')) {{ 'text-violet-500!' }} @endif"
                                             href="{{ route('rencana_diklat.index') }}">
                                             <x-app.sidebar.nav-icon name="calendar-event" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                 class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Rencana
                                                 Diklat</span>
                                         </a>
                                     </li>
                                     <li class="mb-1 last:mb-0">
                                         <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/penghargaan')) {{ 'text-violet-500!' }} @endif"
                                             href="/kepegawaian/penghargaan">
                                             <x-app.sidebar.nav-icon name="medal" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                 class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Penghargaan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/penugasan_ln')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/penugasan_ln">
                                            <x-app.sidebar.nav-icon name="plane" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Penugasan
                                                LN</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/seminar')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/seminar">
                                            <x-app.sidebar.nav-icon name="presentation" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Seminar</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/cuti')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/cuti">
                                            <x-app.sidebar.nav-icon name="calendar-off" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Cuti</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/latihan_jabatan')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/latihan_jabatan">
                                            <x-app.sidebar.nav-icon name="certificate" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Latihan
                                                Jabatan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/mutasi')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/mutasi">
                                            <x-app.sidebar.nav-icon name="arrows-exchange" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Mutasi</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/tunjangan')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/tunjangan">
                                            <x-app.sidebar.nav-icon name="cash" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Tunjungan</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('kepegawaian/izin_kawin')) {{ 'text-violet-500!' }} @endif"
                                            href="/kepegawaian/izin_kawin">
                                            <x-app.sidebar.nav-icon name="heart-handshake" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Izin
                                                Kawin</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
