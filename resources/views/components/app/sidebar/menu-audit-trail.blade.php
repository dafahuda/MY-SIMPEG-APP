<li
                                class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['audit_trail'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif">
                                <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['audit_trail'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                    href="/audit_trail/riwayat_aktivitas">
                                    <div class="flex items-center">
                                        <x-app.sidebar.nav-icon name="history" class="shrink-0 w-4 h-4 @if (in_array(Request::segment(1), ['audit_trail'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif" />
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Riwayat
                                            Aktivitas</span>
                                    </div>
                                </a>
                            </li>
