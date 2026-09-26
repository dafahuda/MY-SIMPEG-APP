<div class="relative inline-flex" x-data="{ open: false }"
     x-init="$watch('open', v => v && $refs.panel && $refs.panel.focus?.())">
    <button
        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 lg:hover:bg-gray-200 dark:hover:bg-gray-700/50
               :class="{ 'bg-gray-200 dark:bg-gray-800': open }"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"
    >
        <span class="sr-only">Notifikasi</span>
        <svg class="fill-current text-gray-500/80 dark:text-gray-400/80" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 0a7 7 0 0 0-7 7c0 1.202.308 2.33.84 3.316l-.789 2.368a1 1 0 0 0 1.265 1.265l2.595-.865a1 1 0 0 0 .441-.283A6.97 6.97 0 0 0 7 14a7 7 0 1 0 0-14Zm3.708 10.364a1 1 0 0 1-1.414 0L7 9.066 5.707 10.36a1 1 0 1 1-1.414-1.414l1.94-1.94a1 1 0 0 1 1.413 0l1.94 1.94a1 1 0 0 1 .322.718 1 1 0 0 1-.2 1.2Z"/>
        </svg>
        @php
            $notifikasiBelumDibaca = auth()->user()
                ? auth()->user()->unreadNotifications()->count()
                : 0;
        @endphp
        @if ($notifikasiBelumDibaca > 0)
            <span class="absolute top-0 right-0 min-w-[18px] h-[18px] px-1 flex items-center justify-center
                         bg-red-500 border-2 border-gray-100 dark:border-gray-900 rounded-full
                         text-[10px] font-bold text-white leading-none">
                {{ $notifikasiBelumDibaca > 99 ? '99+' : $notifikasiBelumDibaca }}
            </span>
        @endif
    </button>
    <div
        class="origin-top-right z-10 absolute top-full -mr-48 sm:mr-0 min-w-80 max-w-96 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 shadow-lg rounded-sm"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        x-ref="panel"
        tabindex="-1"
    >
        @php
            $semuaNotifikasi = auth()->user()
                ? auth()->user()->notifications()->latest('created_at')->limit(10)->get()
                : collect();
        @endphp

        <div class="flex items-center justify-between pt-1.5 pb-2 px-4">
            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Notifikasi</div>
            @if (auth()->user() && auth()->user()->unreadNotifications()->count() > 0)
                <button type="button"
                        class="text-xs font-medium text-violet-500 hover:text-violet-600 dark:text-violet-400"
                        @click="fetch('{{ route('notifications.readAll') }}', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest'}}).then(() => window.location.reload())">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        @if ($semuaNotifikasi->isEmpty())
            <div class="py-6 px-4 text-center text-sm text-gray-400 dark:text-gray-500">
                🔕 Belum ada notifikasi.
            </div>
        @else
            <ul class="max-h-96 overflow-y-auto">
                @foreach ($semuaNotifikasi as $notif)
                    @php
                        $data = $notif->data;
                        $icon = match ($data['judul'] ?? '') {
                            'KGB Jatuh Tempo' => '💰',
                            default => '📣',
                        };
                    @endphp
                    <li class="border-b border-gray-200 dark:border-gray-700/60 last:border-0">
                        <a class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/20 {{ $notif->read_at ? 'opacity-70' : '' }}"
                           href="{{ $data['url'] ?? '#' }}"
                           @click="open = false"
                           @focusin="open = true">
                            <span class="flex items-start gap-2">
                                <span class="text-sm">{{ $icon }}</span>
                                <span class="flex-1 min-w-0">
                                    @if (empty($notif->read_at))
                                        <span class="inline-block w-2 h-2 rounded-full bg-violet-500 mt-1 mr-1 shrink-0" title="Belum dibaca"></span>
                                    @endif
                                    <span class="block text-sm font-medium text-gray-800 dark:text-gray-100">{{ $data['judul'] ?? 'Notifikasi' }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5 break-words">{{ $data['pesan'] ?? '' }}</span>
                                    <span class="block text-xs font-medium text-gray-400 dark:text-gray-500 mt-1">{{ \Illuminate\Support\Carbon::parse($notif->created_at)->translatedFormat('d M Y, H:i') }}</span>
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
