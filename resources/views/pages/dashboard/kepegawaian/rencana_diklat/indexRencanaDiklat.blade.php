<x-app-layout>
    @php
        $activeFilterCount = collect($filters ?? [])->filter(fn($value) => filled($value))->count();
    @endphp

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Kepegawaian</span>
                    <span class="mx-1">/</span>
                    <span>Rencana Diklat</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data Rencana Diklat</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Pantau rencana diklat per pegawai dengan filter terstruktur dan status yang mudah dipindai.
                </p>
            </div>

            @if ($canMutate)
                <a href="{{ route('rencana_diklat.create') }}" data-testid="rencana-diklat-create-button"
                    class="btn bg-indigo-500 hover:bg-indigo-600 text-white shrink-0">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Tambah Rencana Diklat</span>
                </a>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form action="{{ route('rencana_diklat.index') }}" method="GET" data-testid="rencana-diklat-filter-form"
                class="grid gap-4 xl:grid-cols-[minmax(0,1.5fr)_minmax(0,1.1fr)_180px_180px_auto] xl:items-end">
                <div class="flex flex-col gap-1">
                    <label for="rencana_search" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cari rencana</label>
                    <input id="rencana_search" type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        data-testid="rencana-diklat-filter-search" aria-label="Cari Rencana Diklat"
                        placeholder="Nama diklat, pegawai, kompetensi, atau penyelenggara"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                </div>

                <div class="flex flex-col gap-1">
                    <label for="pegawai_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai</label>
                    <select id="pegawai_filter" name="pegawai_id" data-testid="rencana-diklat-filter-pegawai"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">Semua pegawai</option>
                        @foreach ($pegawaiFilterOptions as $pegawai)
                            <option value="{{ $pegawai->id }}" {{ (string) ($filters['pegawai_id'] ?? '') === (string) $pegawai->id ? 'selected' : '' }}>
                                {{ $pegawai->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="tahun_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                    <select id="tahun_filter" name="tahun_rencana" data-testid="rencana-diklat-filter-tahun"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">Semua tahun</option>
                        @foreach ($tahunFilterOptions as $tahun)
                            <option value="{{ $tahun }}" {{ (string) ($filters['tahun_rencana'] ?? '') === (string) $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="status_filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status_filter" name="status" data-testid="rencana-diklat-filter-status"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">Semua status</option>
                        <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="planned" {{ ($filters['status'] ?? '') === 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="realized" {{ ($filters['status'] ?? '') === 'realized' ? 'selected' : '' }}>Realized</option>
                        <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 xl:justify-end">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M15.7 14.3l-3.7-3.7c.9-1.2 1.4-2.6 1.4-4.1C13.4 2.9 10.5 0 7 0S.6 2.9.6 6.5 3.5 13 7 13c1.5 0 2.9-.5 4.1-1.4l3.7 3.7.9-.9zM2 6.5C2 3.7 4.2 1.5 7 1.5S12 3.7 12 6.5 9.8 11.5 7 11.5 2 9.3 2 6.5z" />
                        </svg>
                        Terapkan
                    </button>
                    <a href="{{ route('rencana_diklat.index') }}" data-testid="rencana-diklat-filter-reset"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 text-gray-700 dark:text-gray-200 text-sm font-medium rounded shadow-sm whitespace-nowrap">
                        Reset
                    </a>
                </div>
            </form>

            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium text-gray-600 dark:text-gray-300">Legenda status:</span>
                @include('pages.dashboard.kepegawaian.rencana_diklat._statusBadge', ['status' => 'draft'])
                @include('pages.dashboard.kepegawaian.rencana_diklat._statusBadge', ['status' => 'planned'])
                @include('pages.dashboard.kepegawaian.rencana_diklat._statusBadge', ['status' => 'realized'])
                @include('pages.dashboard.kepegawaian.rencana_diklat._statusBadge', ['status' => 'cancelled'])
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                        Menampilkan {{ $rencanaDiklat->count() }} dari {{ $rencanaDiklat->total() }} rencana diklat
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Gunakan kombinasi pencarian, pegawai, tahun, dan status untuk mempersempit daftar.</p>
                </div>

                @if ($activeFilterCount > 0)
                    <span class="inline-flex items-center rounded-full bg-violet-100 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300 px-3 py-1 text-xs font-semibold">
                        {{ $activeFilterCount }} filter aktif
                    </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table data-testid="rencana-diklat-table" class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50 border-t border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">No</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Pegawai</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Tahun</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Nama Diklat Rencana</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Kategori</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Prioritas</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Target Jam</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Status</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($rencanaDiklat as $data)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-gray-800 dark:text-gray-100">{{ ($rencanaDiklat->firstItem() ?? 1) + $loop->index }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $data->pegawai->nama ?? '-' }}</div>
                                    @if ($data->pegawai?->nip)
                                        <div class="text-xs text-gray-400">{{ $data->pegawai->nip }}</div>
                                    @endif
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left font-medium text-gray-800 dark:text-gray-100">{{ $data->tahun_rencana }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $data->nama_diklat_rencana }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $data->target_kompetensi }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left">{{ $data->kategori_diklat }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left">{{ $data->prioritas }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left">{{ $data->target_jam }} jam</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    @include('pages.dashboard.kepegawaian.rencana_diklat._statusBadge', ['status' => $data->status])
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    @if ($canMutate)
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('rencana_diklat.edit', $data->id) }}" class="inline-block py-2 px-3 text-white bg-blue-500 hover:bg-blue-700 rounded-lg shadow-lg">Edit</a>
                                            <form action="{{ route('rencana_diklat.destroy', $data->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-block py-2 px-3 text-white bg-red-500 hover:bg-red-700 rounded-lg shadow-lg">Delete</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Read-only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada data rencana diklat yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex justify-center">
            {{ $rencanaDiklat->links() }}
        </div>
    </div>
</x-app-layout>
