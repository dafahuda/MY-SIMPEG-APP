<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Report</span>
                    <span>/</span>
                    <span>Pensiun</span>
                    @if ($selectedPeriode)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $periodeOptions[$selectedPeriode] ?? '' }}</span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Report <span class="text-base font-normal text-gray-500 dark:text-gray-400">Pensiun</span>
                </h1>
            </div>
        </div>

        {{-- Filter Card --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form method="GET" action="{{ route('report.pensiun') }}"
                class="flex flex-col sm:flex-row items-end gap-3">
                <div class="flex flex-col gap-1 flex-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Pilih Periode
                    </label>
                    <select name="periode" id="periodeSelect"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">-- Pilih Periode --</option>
                        @foreach ($periodeOptions as $val => $label)
                            <option value="{{ $val }}" {{ $selectedPeriode === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path d="M15.7 14.3l-3.7-3.7c.9-1.2 1.4-2.6 1.4-4.1C13.4 2.9 10.5 0 7 0S.6 2.9.6 6.5 3.5 13 7 13c1.5 0 2.9-.5 4.1-1.4l3.7 3.7.9-.9zM2 6.5C2 3.7 4.2 1.5 7 1.5S12 3.7 12 6.5 9.8 11.5 7 11.5 2 9.3 2 6.5z"/>
                    </svg>
                    Get Report
                </button>
            </form>
        </div>

        {{-- Table Card (selalu tampil setelah filter) --}}
        @if ($selectedPeriode !== '')
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">

                {{-- Table controls --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span>Show</span>
                        <select id="perPageSelect"
                            class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span>entries</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <label class="text-gray-500 dark:text-gray-400">Search:</label>
                        <input type="text" id="tableSearch"
                            class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                            placeholder="Cari..." />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-t border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 py-3 text-center w-10">
                                    <div class="flex items-center justify-center gap-1">No
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16"><path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z"/></svg>
                                    </div>
                                </th>
                                <th class="px-3 py-3">
                                    <div class="flex items-center gap-1">NIP
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16"><path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z"/></svg>
                                    </div>
                                </th>
                                <th class="px-3 py-3">
                                    <div class="flex items-center gap-1">Nama
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16"><path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z"/></svg>
                                    </div>
                                </th>
                                <th class="px-3 py-3">
                                    <div class="flex items-center gap-1">TTL
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16"><path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z"/></svg>
                                    </div>
                                </th>
                                <th class="px-3 py-3">
                                    <div class="flex items-center gap-1">IK
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16"><path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z"/></svg>
                                    </div>
                                </th>
                                <th class="px-3 py-3">
                                    <div class="flex items-center gap-1">No. Telp
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16"><path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z"/></svg>
                                    </div>
                                </th>
                                <th class="px-3 py-3">
                                    <div class="flex items-center gap-1">Periode Pensiun
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16"><path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z"/></svg>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($pegawaiList as $index => $pegawai)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 table-row">
                                    <td class="px-3 py-3 text-center row-no text-gray-700 dark:text-gray-300">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ $pegawai->nip ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}
                                        </div>
                                        @if ($pegawai->unit_kerja)
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $pegawai->unit_kerja->nama_unit }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ $pegawai->tmpt_lahir }},
                                        {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') }}
                                    </td>
                                    <td class="px-3 py-3 text-center capitalize text-gray-700 dark:text-gray-300">
                                        {{ $pegawai->jenis_kelamin === 'laki-laki' ? 'L' : 'P' }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                        {{ $pegawai->no_hp ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                            {{ $pegawai->periode_pensiun }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500">
                                        No data available in table
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-t border-gray-200 dark:border-gray-700 gap-3">
                    <div id="tableInfo" class="text-sm text-gray-500 dark:text-gray-400">
                        Showing 0 to 0 of 0 entries
                    </div>
                    <div class="flex items-center gap-1 text-sm">
                        <button id="prevBtn"
                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed"
                            disabled>Previous</button>
                        <span id="pageNumbers" class="flex gap-1"></span>
                        <button id="nextBtn"
                            class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @push('scripts')
    <script>
    (function () {
        const allRows    = Array.from(document.querySelectorAll('#tableBody .table-row'));
        const tableInfo  = document.getElementById('tableInfo');
        const prevBtn    = document.getElementById('prevBtn');
        const nextBtn    = document.getElementById('nextBtn');
        const pageNums   = document.getElementById('pageNumbers');
        const searchInput = document.getElementById('tableSearch');
        const perPageSel  = document.getElementById('perPageSelect');

        if (!allRows.length) {
            if (tableInfo) tableInfo.textContent = 'Showing 0 to 0 of 0 entries';
            return;
        }

        let currentPage = 1;
        let perPage     = parseInt(perPageSel?.value ?? 10);
        let filtered    = [...allRows];

        function applySearch() {
            const q = (searchInput?.value ?? '').toLowerCase().trim();
            filtered = allRows.filter(r => r.textContent.toLowerCase().includes(q));
            currentPage = 1;
            render();
        }

        function render() {
            const total      = filtered.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            currentPage      = Math.min(currentPage, totalPages);
            const start      = (currentPage - 1) * perPage;
            const end        = Math.min(start + perPage, total);

            allRows.forEach(r => r.style.display = 'none');
            filtered.forEach((r, i) => {
                r.style.display = (i >= start && i < end) ? '' : 'none';
                if (i >= start && i < end) {
                    const noCell = r.querySelector('.row-no');
                    if (noCell) noCell.textContent = i + 1;
                }
            });

            if (tableInfo) {
                tableInfo.textContent = total === 0
                    ? 'Showing 0 to 0 of 0 entries'
                    : `Showing ${start + 1} to ${end} of ${total} entries`;
            }

            if (prevBtn) prevBtn.disabled = currentPage <= 1;
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages;

            if (pageNums) {
                pageNums.innerHTML = '';
                buildPageRange(currentPage, totalPages).forEach(p => {
                    if (p === '...') {
                        const s = document.createElement('span');
                        s.textContent = '...';
                        s.className = 'px-2 py-1 text-gray-400';
                        pageNums.appendChild(s);
                    } else {
                        const btn = document.createElement('button');
                        btn.textContent = p;
                        btn.className = p === currentPage
                            ? 'px-3 py-1 rounded bg-indigo-500 text-white text-sm font-medium'
                            : 'px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm';
                        btn.addEventListener('click', () => { currentPage = p; render(); });
                        pageNums.appendChild(btn);
                    }
                });
            }
        }

        function buildPageRange(cur, total) {
            if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
            const pages = [1];
            if (cur > 3) pages.push('...');
            for (let i = Math.max(2, cur - 1); i <= Math.min(total - 1, cur + 1); i++) pages.push(i);
            if (cur < total - 2) pages.push('...');
            pages.push(total);
            return pages;
        }

        searchInput?.addEventListener('input', applySearch);
        perPageSel?.addEventListener('change', () => { perPage = parseInt(perPageSel.value); currentPage = 1; render(); });
        prevBtn?.addEventListener('click', () => { if (currentPage > 1) { currentPage--; render(); } });
        nextBtn?.addEventListener('click', () => {
            if (currentPage < Math.ceil(filtered.length / perPage)) { currentPage++; render(); }
        });

        render();
    })();
    </script>
    @endpush
</x-app-layout>
