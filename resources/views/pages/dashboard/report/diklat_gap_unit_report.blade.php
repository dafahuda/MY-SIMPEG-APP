<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Report</span>
                    <span>/</span>
                    <span>Diklat Gap Unit</span>
                    @if ($selectedUnitKerja)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedUnitKerja->nama_unit }}</span>
                    @else
                        <span>/</span>
                        <span class="text-blue-500 font-medium">Semua Unit</span>
                    @endif
                    @if ($selectedTahunRencana)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedTahunRencana }}</span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Report <span class="text-base font-normal text-gray-500 dark:text-gray-400">Diklat Gap Unit/Tahun</span>
                </h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form method="GET" action="{{ route('report.diklat_gap.unit') }}" data-testid="diklat-gap-unit-filter-form"
                class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Rencana</label>
                    <select name="tahun_rencana" required data-testid="diklat-gap-unit-tahun-rencana-select" data-selected="{{ $selectedTahunRencana ?? '' }}"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">-- Pilih Tahun --</option>
                        @for ($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ (string) $selectedTahunRencana === (string) $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Realisasi</label>
                    <select name="tahun_realisasi" data-testid="diklat-gap-unit-tahun-realisasi-select" data-selected="{{ $selectedTahunRealisasi ?? '' }}"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">-- Default ke Tahun Rencana --</option>
                        @for ($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ (string) $selectedTahunRealisasi === (string) $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                @if ($unitKerjaOptions->isNotEmpty())
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Unit Kerja</label>
                        <select name="unit_kerja_id" data-testid="diklat-gap-unit-select" data-selected="{{ $selectedUnitKerjaId ?? '' }}"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                            <option value="">-- Semua Unit --</option>
                            @foreach ($unitKerjaOptions as $unitKerja)
                                <option value="{{ $unitKerja->id }}" {{ (string) $selectedUnitKerjaId === (string) $unitKerja->id ? 'selected' : '' }}>
                                    {{ $unitKerja->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="unit_kerja_id" value="{{ $selectedUnitKerjaId ?? '' }}">
                @endif

                <div class="flex items-end gap-2 flex-wrap">
                    <button type="submit" data-testid="diklat-gap-unit-submit-button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                        Get Report
                    </button>

                    @if ($reportReady)
                        <a href="{{ route('report.diklat_gap.unit.print', $filterQuery) }}" data-testid="diklat-gap-unit-print-button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                            Print
                        </a>
                        <a href="{{ route('report.diklat_gap.unit.export', $filterQuery) }}" data-testid="diklat-gap-unit-export-button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                            Export Excel
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if ($reportReady)
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 mb-6">
                @foreach ([
                    'planned' => ['label' => 'Planned', 'value' => $summary['planned_count']],
                    'realized' => ['label' => 'Realized Linked', 'value' => $summary['realized_linked_count']],
                    'not_realized' => ['label' => 'Not Realized', 'value' => $summary['not_realized_count']],
                    'out_of_plan' => ['label' => 'Out of Plan', 'value' => $summary['out_of_plan_count']],
                    'cross_year_realized' => ['label' => 'Cross Year Realized', 'value' => $summary['cross_year_realized_count']],
                    'planned_hours' => ['label' => 'Planned Hours', 'value' => $summary['planned_hours']],
                    'realized_linked_hours' => ['label' => 'Realized Hours', 'value' => $summary['realized_linked_hours']],
                    'hour_gap' => ['label' => 'Hour Gap', 'value' => $summary['hour_gap']],
                ] as $key => $metric)
                    <div data-testid="diklat-gap-unit-summary-card-{{ $key }}" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</div>
                        <div class="mt-2 text-2xl font-bold text-gray-800 dark:text-gray-100" data-testid="diklat-gap-unit-summary-value-{{ $key }}" data-value="{{ $metric['value'] }}">{{ number_format($metric['value']) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Unit Aggregate Rows</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Detail plan, realisasi, dan out-of-plan pada unit/tahun terpilih.</p>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400" data-testid="diklat-gap-unit-row-count" data-value="{{ $exportRows->count() }}">{{ $exportRows->count() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-3">Pegawai</th>
                                <th class="px-3 py-3">Unit</th>
                                <th class="px-3 py-3">Tahun Rencana</th>
                                <th class="px-3 py-3">Tahun Realisasi</th>
                                <th class="px-3 py-3">Nama Rencana</th>
                                <th class="px-3 py-3">Nama Realisasi</th>
                                <th class="px-3 py-3">Bucket Status</th>
                                <th class="px-3 py-3 text-right">Target Jam</th>
                                <th class="px-3 py-3 text-right">Realisasi Jam</th>
                                <th class="px-3 py-3 text-right">Gap Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($exportRows as $index => $row)
                                <tr data-testid="diklat-gap-unit-row-{{ $index }}">
                                    <td class="px-3 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $row['pegawai'] }}</td>
                                    <td class="px-3 py-3">{{ $row['unit'] }}</td>
                                    <td class="px-3 py-3">{{ $row['tahun_rencana'] }}</td>
                                    <td class="px-3 py-3">{{ $row['tahun_realisasi'] }}</td>
                                    <td class="px-3 py-3">{{ $row['nama_rencana'] }}</td>
                                    <td class="px-3 py-3">{{ $row['nama_realisasi'] }}</td>
                                    <td class="px-3 py-3">{{ $row['bucket_label'] }}</td>
                                    <td class="px-3 py-3 text-right">{{ number_format($row['target_jam']) }}</td>
                                    <td class="px-3 py-3 text-right">{{ number_format($row['realisasi_jam']) }}</td>
                                    <td class="px-3 py-3 text-right">{{ number_format($row['gap_jam']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-3 py-6 text-center text-gray-400">Tidak ada data unit aggregate.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-6 text-sm text-gray-500 dark:text-gray-400">
                Pilih tahun rencana untuk menampilkan report gap diklat per unit.
            </div>
        @endif
    </div>
</x-app-layout>
