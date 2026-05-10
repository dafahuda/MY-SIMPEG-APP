<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Report</span>
                    <span class="mx-1">/</span>
                    <span>Tambahan Penghasilan Pegawai / TPP</span>
                    @if ($selectedBulan && $selectedTahun)
                        <span class="mx-1">/</span>
                        <span>Periode:
                            <strong class="text-blue-500">{{ $selectedBulan }} {{ $selectedTahun }}</strong>
                        </span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Report
                    <span class="text-base font-normal text-gray-500 dark:text-gray-400">Tambahan Penghasilan Pegawai /
                        TPP</span>
                </h1>
            </div>
            @if ($selectedBulan && $selectedTahun && $data->isNotEmpty())
                <div>
                    <a href="{{ route('tpp.export_excel_laporan_tpp', ['periode' => $selectedBulan, 'tahun' => $selectedTahun]) }}" class="btn bg-green-500 hover:bg-green-600 text-white">
                        <svg class="w-4 h-4 fill-current opacity-80 shrink-0" viewBox="0 0 16 16">
                            <path d="M8 12l-4-4h2.5V2h3v6H12L8 12zm-5 2h10v-2H3v2z" />
                        </svg>
                        <span class="ml-2">Export</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Filter Card -->
        <div
            class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form method="GET" action="{{ route('tpp.laporan_bulanan') }}"
                class="flex flex-col sm:flex-row items-end gap-3">
                <div class="flex flex-col gap-1 flex-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Periode</label>
                    <div class="flex gap-3">
                        <select name="periode"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                            <option value="">-- Bulan --</option>
                            @foreach ($bulanList as $bulan)
                                <option value="{{ $bulan }}" {{ $selectedBulan === $bulan ? 'selected' : '' }}>
                                    {{ $bulan }}
                                </option>
                            @endforeach
                        </select>
                        <select name="tahun"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                            <option value="">-- Tahun --</option>
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path
                            d="M15.7 14.3l-3.7-3.7c.9-1.2 1.4-2.6 1.4-4.1C13.4 2.9 10.5 0 7 0S.6 2.9.6 6.5 3.5 13 7 13c1.5 0 2.9-.5 4.1-1.4l3.7 3.7.9-.9zM2 6.5C2 3.7 4.2 1.5 7 1.5S12 3.7 12 6.5 9.8 11.5 7 11.5 2 9.3 2 6.5z" />
                    </svg>
                    Get Report
                </button>
            </form>
        </div>

        @if ($selectedBulan && $selectedTahun)
            <!-- Result Card -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">

                <!-- Table top controls -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span>Show</span>
                        <select id="perPageSelect"
                            class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
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
                    <table id="laporanTable" class="w-full text-sm text-left text-body">
                        <thead
                            class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-t border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-left">No</div>
                                </th>
                                <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap cursor-pointer select-none"
                                    data-col="0">
                                    <div class="font-semibold text-left flex items-center gap-1">
                                        Pegawai
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16">
                                            <path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap cursor-pointer select-none"
                                    data-col="1">
                                    <div class="font-semibold text-left flex items-center gap-1">
                                        Jabatan
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16">
                                            <path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap cursor-pointer select-none"
                                    data-col="2">
                                    <div class="font-semibold text-right flex items-center justify-end gap-1">
                                        Nilai Basic TPP
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16">
                                            <path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap cursor-pointer select-none"
                                    data-col="3">
                                    <div class="font-semibold text-right flex items-center justify-end gap-1">
                                        Pengurangan Produktifitas
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16">
                                            <path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap cursor-pointer select-none"
                                    data-col="4">
                                    <div class="font-semibold text-right flex items-center justify-end gap-1">
                                        Pengurangan Disiplin
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16">
                                            <path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap cursor-pointer select-none"
                                    data-col="5">
                                    <div class="font-semibold text-right flex items-center justify-end gap-1">
                                        TPP Diterima
                                        <svg class="w-3 h-3 fill-current opacity-50" viewBox="0 0 16 16">
                                            <path d="M8 1l4 6H4l4-6zm0 14l-4-6h8l-4 6z" />
                                        </svg>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($data as $index => $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 table-row">
                                    <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap row-no">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                        <div class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ $row->pegawai->nama ?? '-' }}
                                        </div>
                                        @if ($row->pegawai && $row->pegawai->nip)
                                            <div class="text-xs text-gray-400">{{ $row->pegawai->nip }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                        <div class="text-gray-700 dark:text-gray-300">
                                            {{ $row->pegawai->jabatan_aktif->master_jabatan->nama_jabatan ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                        <div class="text-right text-gray-700 dark:text-gray-300">
                                            {{ number_format($row->nilai_basic_tpp, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                        <div class="text-right text-gray-700 dark:text-gray-300">
                                            {{ number_format($row->pengurangan_produktifitas, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                        <div class="text-right text-gray-700 dark:text-gray-300">
                                            {{ number_format($row->pengurangan_disiplin, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                        <div class="text-right font-semibold text-gray-800 dark:text-gray-100">
                                            {{ number_format($row->tpp_diterima, 0, ',', '.') }}
                                        </div>
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

                <!-- Table bottom: info + pagination -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-t border-gray-200 dark:border-gray-700 gap-3">
                    <div id="tableInfo" class="text-sm text-gray-500 dark:text-gray-400">
                        Showing 0 to 0 of 0 entries
                    </div>
                    <div id="tablePagination" class="flex items-center gap-1 text-sm">
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
            (function() {
                const allRows = Array.from(document.querySelectorAll('#tableBody .table-row'));
                const tableInfo = document.getElementById('tableInfo');
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const pageNums = document.getElementById('pageNumbers');
                const searchInput = document.getElementById('tableSearch');
                const perPageSel = document.getElementById('perPageSelect');

                if (!allRows.length) {
                    if (tableInfo) tableInfo.textContent = 'Showing 0 to 0 of 0 entries';
                    return;
                }

                let currentPage = 1;
                let perPage = parseInt(perPageSel?.value ?? 10);
                let filtered = [...allRows];

                // --- Search ---
                function applySearch() {
                    const q = (searchInput?.value ?? '').toLowerCase().trim();
                    filtered = allRows.filter(row => row.textContent.toLowerCase().includes(q));
                    currentPage = 1;
                    render();
                }

                // --- Render ---
                function render() {
                    const total = filtered.length;
                    const totalPages = Math.max(1, Math.ceil(total / perPage));
                    currentPage = Math.min(currentPage, totalPages);

                    const start = (currentPage - 1) * perPage;
                    const end = Math.min(start + perPage, total);

                    // Hide all, show only current page slice
                    allRows.forEach(r => r.style.display = 'none');
                    filtered.forEach((r, i) => {
                        r.style.display = (i >= start && i < end) ? '' : 'none';
                        // Re-number visible rows
                        if (i >= start && i < end) {
                            const noCell = r.querySelector('.row-no');
                            if (noCell) noCell.textContent = i + 1;
                        }
                    });

                    // Info text
                    if (tableInfo) {
                        tableInfo.textContent = total === 0 ?
                            'Showing 0 to 0 of 0 entries' :
                            `Showing ${start + 1} to ${end} of ${total} entries`;
                    }

                    // Prev / Next
                    if (prevBtn) prevBtn.disabled = currentPage <= 1;
                    if (nextBtn) nextBtn.disabled = currentPage >= totalPages;

                    // Page numbers
                    if (pageNums) {
                        pageNums.innerHTML = '';
                        const range = buildPageRange(currentPage, totalPages);
                        range.forEach(p => {
                            if (p === '...') {
                                const span = document.createElement('span');
                                span.textContent = '...';
                                span.className = 'px-2 py-1 text-gray-400';
                                pageNums.appendChild(span);
                            } else {
                                const btn = document.createElement('button');
                                btn.textContent = p;
                                btn.className = p === currentPage ?
                                    'px-3 py-1 rounded bg-indigo-500 text-white text-sm font-medium' :
                                    'px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm';
                                btn.addEventListener('click', () => {
                                    currentPage = p;
                                    render();
                                });
                                pageNums.appendChild(btn);
                            }
                        });
                    }
                }

                function buildPageRange(current, total) {
                    if (total <= 7) return Array.from({
                        length: total
                    }, (_, i) => i + 1);
                    const pages = [1];
                    if (current > 3) pages.push('...');
                    for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) pages.push(i);
                    if (current < total - 2) pages.push('...');
                    pages.push(total);
                    return pages;
                }

                // Events
                searchInput?.addEventListener('input', applySearch);
                perPageSel?.addEventListener('change', () => {
                    perPage = parseInt(perPageSel.value);
                    currentPage = 1;
                    render();
                });
                prevBtn?.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        render();
                    }
                });
                nextBtn?.addEventListener('click', () => {
                    const total = filtered.length;
                    if (currentPage < Math.ceil(total / perPage)) {
                        currentPage++;
                        render();
                    }
                });

                // Initial render
                render();
            })();
        </script>
    @endpush
</x-app-layout>
