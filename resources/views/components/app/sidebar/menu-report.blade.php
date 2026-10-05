<li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['report'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif"
                            x-data="{ open: {{ in_array(Request::segment(1), ['report']) ? 1 : 0 }} }">
                            <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['report'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                href="#0" @click.prevent="open = !open; sidebarExpanded = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <x-app.sidebar.nav-icon name="report-analytics" class="shrink-0 w-4 h-4 @if (in_array(Request::segment(1), ['report'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif" />
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Report</span>
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
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('report.diklat_gap')) {{ 'text-violet-500!' }} @endif"
                                            href="{{ route('report.diklat_gap') }}" data-testid="sidebar-report-diklat-gap-link">
                                            <x-app.sidebar.nav-icon name="filter-search" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gap Diklat Pegawai</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('report.diklat_gap.unit') || Route::is('report.diklat_gap.unit.print') || Route::is('report.diklat_gap.unit.export')) {{ 'text-violet-500!' }} @endif"
                                            href="{{ route('report.diklat_gap.unit') }}" data-testid="sidebar-report-diklat-gap-unit-link">
                                            <x-app.sidebar.nav-icon name="chart-dots" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Gap Diklat Unit</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('report/nominatif')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/nominatif">
                                            <x-app.sidebar.nav-icon name="list-details" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">
                                                Nominatif</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('report/duk')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/duk">
                                            <x-app.sidebar.nav-icon name="list-numbers" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">DUK</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('report/bezetting')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/bezetting">
                                            <x-app.sidebar.nav-icon name="chart-histogram" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Bezetting</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('report/keadaan_pegawai')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/keadaan_pegawai">
                                            <x-app.sidebar.nav-icon name="users" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Keadaan
                                                Pegawai</span>
                                        </a>
                                    </li>
                                    <li class="mb-1 last:mb-0">
                                        <a class="flex items-center gap-2 text-gray-500/90 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition truncate @if (Route::is('report/pensiun')) {{ 'text-violet-500!' }} @endif"
                                            href="/report/pensiun">
                                            <x-app.sidebar.nav-icon name="history" class="w-3.5 h-3.5 shrink-0" />
                                            <span
                                                class="text-sm font-medium lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Pensiun</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
