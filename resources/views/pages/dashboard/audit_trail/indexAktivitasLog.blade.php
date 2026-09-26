<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Riwayat Aktivitas (Audit Trail)</h1>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="cariLog" aria-label="Cari aktivitas" placeholder="Cari deskripsi / modul / pengguna..."
                       value="{{ request('cariLog') }}" class="rounded-lg">
                <select name="aksi" class="rounded-lg" aria-label="Filter aksi">
                    <option value="">Semua Aksi</option>
                    @foreach (['buat', 'ubah', 'hapus'] as $a)
                        <option value="{{ $a }}" @selected(request('aksi') === $a)>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
                <select name="modul" class="rounded-lg" aria-label="Filter modul">
                    <option value="">Semua Modul</option>
                    @foreach ($daftarModul as $m)
                        <option value="{{ $m }}" @selected(request('modul') === $m)>{{ $m }}</option>
                    @endforeach
                </select>
                <button class="inline-block text-white rounded-lg shadow-lg px-3 py-2 bg-blue-500 hover:bg-blue-700">
                    Filter
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50 border-t border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Waktu</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Pengguna</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Aksi</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Modul</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Deskripsi</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">IP</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                    {{ $log->created_at->format('d-m-Y H:i:s') }}
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $log->username_snapshot ?? 'Sistem' }}
                                    </div>
                                    @if ($log->user?->role)
                                        <div class="text-[11px] uppercase text-gray-400">{{ $log->user->role }}</div>
                                    @endif
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    @if ($log->aksi === 'buat')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Buat</span>
                                    @elseif ($log->aksi === 'ubah')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Ubah</span>
                                    @elseif ($log->aksi === 'hapus')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">Hapus</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ ucfirst($log->aksi) }}</span>
                                    @endif
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                    {{ $log->modul }}
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 text-gray-800 dark:text-gray-100">
                                    {{ $log->deskripsi }}
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">
                                    {{ $log->ip_address }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-2 first:pl-5 last:pr-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $logs->links() }}
        </div>

    </div>
</x-app-layout>
