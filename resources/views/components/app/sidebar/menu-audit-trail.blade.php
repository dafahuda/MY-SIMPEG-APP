<li
                                class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 bg-linear-to-r @if (in_array(Request::segment(1), ['audit_trail'])) {{ 'from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]' }} @endif">
                                <a class="block text-gray-800 dark:text-gray-100 truncate transition @if (!in_array(Request::segment(1), ['audit_trail'])) {{ 'hover:text-gray-900 dark:hover:text-white' }} @endif"
                                    href="/audit_trail/riwayat_aktivitas">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current @if (in_array(Request::segment(1), ['audit_trail'])) {{ 'text-violet-500' }}@else{{ 'text-gray-400 dark:text-gray-500' }} @endif"
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 16 16">
                                            <path d="M8 1.5a6.5 6.5 0 1 0 3.25 12.13l-.75-1.3A5 5 0 1 1 12.98 8H10.5l2.75 2.75-.9.9L8.3 8.6a1 1 0 0 1 0-1.2l3.05-3.05.9.9L10.5 6.5h2.48A6.5 6.5 0 0 0 8 1.5z"/>
                                        </svg>
                                        <span
                                            class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">Riwayat
                                            Aktivitas</span>
                                    </div>
                                </a>
                            </li>
