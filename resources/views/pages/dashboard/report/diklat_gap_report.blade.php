<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Laporan</span>
                    <span>/</span>
                    <span>Evaluasi Gap Diklat Pegawai</span>
                    @if ($selectedPegawai)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedPegawai->nama }}</span>
                    @elseif ($selectedUnitKerja)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedUnitKerja->nama_unit }}</span>
                    @endif
                    @if ($selectedTahunRencana)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedTahunRencana }}</span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Laporan Kesenjangan Diklat Pegawai</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Laporan ini membandingkan rencana diklat dengan data realisasi resmi; pengajuan yang masih menunggu verifikasi atau revisi tidak dihitung sebagai riwayat resmi.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form method="GET" action="{{ route('report.diklat_gap') }}" data-testid="diklat-gap-filter-form"
                class="grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Rencana</label>
                    <select name="tahun_rencana" required data-testid="diklat-gap-tahun-rencana-select" data-selected="{{ $selectedTahunRencana ?? '' }}"
                        class="form-select w-full">
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
                    <select name="tahun_realisasi" data-testid="diklat-gap-tahun-realisasi-select" data-selected="{{ $selectedTahunRealisasi ?? '' }}"
                        class="form-select w-full">
                        <option value="">-- Gunakan Tahun Rencana --</option>
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
                            class="form-select w-full">
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

                @if ($pegawaiOptions->isNotEmpty())
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pegawai</label>
                        <select name="pegawai_id" data-testid="diklat-gap-pegawai-select" data-selected="{{ $selectedPegawaiId ?? '' }}"
                            class="form-select w-full">
                            <option value="">-- Semua Pegawai --</option>
                            @foreach ($pegawaiOptions as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ (string) $selectedPegawaiId === (string) $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama }} @if ($pegawai->unit_kerja) ({{ $pegawai->unit_kerja->nama_unit }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="pegawai_id" value="{{ $selectedPegawaiId ?? '' }}">
                @endif

                <div class="flex items-end">
                    <button type="submit" data-testid="diklat-gap-submit-button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M15.7 14.3l-3.7-3.7c.9-1.2 1.4-2.6 1.4-4.1C13.4 2.9 10.5 0 7 0S.6 2.9.6 6.5 3.5 13 7 13c1.5 0 2.9-.5 4.1-1.4l3.7 3.7.9-.9zM2 6.5C2 3.7 4.2 1.5 7 1.5S12 3.7 12 6.5 9.8 11.5 7 11.5 2 9.3 2 6.5z" />
                        </svg>
                        {{ \App\Support\DiklatGlossary::uiLabel('get_report') }}
                    </button>
                </div>
            </form>
        </div>

        @if ($reportReady)
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 mb-6">
                @foreach ([
                    'planned' => ['label' => \App\Support\DiklatGlossary::reportLabel('planned'), 'value' => $summary['planned_count']],
                    'realized' => ['label' => \App\Support\DiklatGlossary::reportLabel('realized'), 'value' => $summary['realized_linked_count']],
                    'not_realized' => ['label' => \App\Support\DiklatGlossary::reportLabel('not_realized'), 'value' => $summary['not_realized_count']],
                    'out_of_plan' => ['label' => \App\Support\DiklatGlossary::reportLabel('out_of_plan'), 'value' => $summary['out_of_plan_count']],
                    'cross_year_realized' => ['label' => \App\Support\DiklatGlossary::reportLabel('cross_year_realized'), 'value' => $summary['cross_year_realized_count']],
                    'planned_hours' => ['label' => \App\Support\DiklatGlossary::reportLabel('planned_hours'), 'value' => $summary['planned_hours']],
                    'realized_linked_hours' => ['label' => \App\Support\DiklatGlossary::reportLabel('realized_linked_hours'), 'value' => $summary['realized_linked_hours']],
                    'hour_gap' => ['label' => \App\Support\DiklatGlossary::reportLabel('hour_gap'), 'value' => $summary['hour_gap']],
                ] as $key => $metric)
                    <div data-testid="diklat-gap-summary-card-{{ $key }}" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</div>
                        <div class="mt-2 text-2xl font-bold text-gray-800 dark:text-gray-100" data-testid="diklat-gap-summary-value-{{ $key }}" data-value="{{ $metric['value'] }}">{{ number_format($metric['value']) }}</div>
                    </div>
                @endforeach
            </div>

            <div class="space-y-6">
                <section data-testid="diklat-gap-section-planned" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ \App\Support\DiklatGlossary::reportLabel('planned') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Rencana aktif pada tahun rencana terpilih.</p>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400" data-testid="diklat-gap-bucket-count-planned" data-value="{{ $plannedRows->count() }}">{{ $plannedRows->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-3">Pegawai</th>
                                    <th class="px-3 py-3">Unit Kerja</th>
                                    <th class="px-3 py-3">Tahun Rencana</th>
                                    <th class="px-3 py-3">Nama Rencana</th>
                                    <th class="px-3 py-3">Status</th>
                                    <th class="px-3 py-3 text-right">Target Jam</th>
                                    <th class="px-3 py-3">Realisasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($plannedRows as $index => $row)
                                    <tr data-testid="diklat-gap-row-planned-{{ $index }}">
                                        <td class="px-3 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $row['pegawai'] }}</td>
                                        <td class="px-3 py-3">{{ $row['unit_kerja'] }}</td>
                                        <td class="px-3 py-3">{{ $row['tahun_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ $row['nama_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ \App\Support\DiklatGlossary::statusLabel($row['status']) }}</td>
                                        <td class="px-3 py-3 text-right">{{ number_format($row['target_jam']) }}</td>
                                        <td class="px-3 py-3">{{ $row['realisasi'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-3 py-6 text-center text-gray-400">Tidak ada data rencana aktif.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section data-testid="diklat-gap-section-realized" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ \App\Support\DiklatGlossary::reportLabel('realized') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Rencana yang sudah benar-benar terealisasi.</p>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400" data-testid="diklat-gap-bucket-count-realized" data-value="{{ $realizedRows->count() }}">{{ $realizedRows->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-3">Pegawai</th>
                                    <th class="px-3 py-3">Unit Kerja</th>
                                    <th class="px-3 py-3">Tahun Rencana</th>
                                    <th class="px-3 py-3">Tahun Realisasi</th>
                                    <th class="px-3 py-3">Rencana</th>
                                    <th class="px-3 py-3">Realisasi</th>
                                    <th class="px-3 py-3 text-right">Selisih Jam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($realizedRows as $index => $row)
                                    <tr data-testid="diklat-gap-row-realized-{{ $index }}">
                                        <td class="px-3 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $row['pegawai'] }}</td>
                                        <td class="px-3 py-3">{{ $row['unit_kerja'] }}</td>
                                        <td class="px-3 py-3">{{ $row['tahun_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ $row['tahun_realisasi'] }}</td>
                                        <td class="px-3 py-3">{{ $row['nama_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ $row['nama_realisasi'] }}</td>
                                        <td class="px-3 py-3 text-right">{{ number_format($row['gap_jam']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-3 py-6 text-center text-gray-400">Tidak ada data realisasi terhubung.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section data-testid="diklat-gap-section-not-realized" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ \App\Support\DiklatGlossary::reportLabel('not_realized') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Rencana yang belum punya realisasi terhubung.</p>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400" data-testid="diklat-gap-bucket-count-not-realized" data-value="{{ $notRealizedRows->count() }}">{{ $notRealizedRows->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-3">Pegawai</th>
                                    <th class="px-3 py-3">Unit Kerja</th>
                                    <th class="px-3 py-3">Tahun Rencana</th>
                                    <th class="px-3 py-3">Nama Rencana</th>
                                    <th class="px-3 py-3">Status</th>
                                    <th class="px-3 py-3 text-right">Target Jam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($notRealizedRows as $index => $row)
                                    <tr data-testid="diklat-gap-row-not-realized-{{ $index }}">
                                        <td class="px-3 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $row['pegawai'] }}</td>
                                        <td class="px-3 py-3">{{ $row['unit_kerja'] }}</td>
                                        <td class="px-3 py-3">{{ $row['tahun_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ $row['nama_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ \App\Support\DiklatGlossary::statusLabel($row['status']) }}</td>
                                        <td class="px-3 py-3 text-right">{{ number_format($row['target_jam']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Tidak ada data yang belum terealisasi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section data-testid="diklat-gap-section-out-of-plan" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ \App\Support\DiklatGlossary::reportLabel('out_of_plan') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Realisasi tanpa rencana terhubung.</p>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400" data-testid="diklat-gap-bucket-count-out-of-plan" data-value="{{ $outOfPlanRows->count() }}">{{ $outOfPlanRows->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-3">Pegawai</th>
                                    <th class="px-3 py-3">Unit Kerja</th>
                                    <th class="px-3 py-3">Tahun Realisasi</th>
                                    <th class="px-3 py-3">Nama Diklat</th>
                                    <th class="px-3 py-3 text-right">Jumlah Jam</th>
                                    <th class="px-3 py-3">Penyelenggara</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($outOfPlanRows as $index => $row)
                                    <tr data-testid="diklat-gap-row-out-of-plan-{{ $index }}">
                                        <td class="px-3 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $row['pegawai'] }}</td>
                                        <td class="px-3 py-3">{{ $row['unit_kerja'] }}</td>
                                        <td class="px-3 py-3">{{ $row['tahun_realisasi'] }}</td>
                                        <td class="px-3 py-3">{{ $row['nama_realisasi'] }}</td>
                                        <td class="px-3 py-3 text-right">{{ number_format($row['jumlah_jam']) }}</td>
                                        <td class="px-3 py-3">{{ $row['penyelenggara'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Tidak ada data di luar rencana.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section data-testid="diklat-gap-section-cross-year-realized" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ \App\Support\DiklatGlossary::reportLabel('cross_year_realized') }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Realisasi yang jatuh satu tahun setelah tahun rencana.</p>
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400" data-testid="diklat-gap-bucket-count-cross-year-realized" data-value="{{ $crossYearRealizedRows->count() }}">{{ $crossYearRealizedRows->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-3 py-3">Pegawai</th>
                                    <th class="px-3 py-3">Unit Kerja</th>
                                    <th class="px-3 py-3">Tahun Rencana</th>
                                    <th class="px-3 py-3">Tahun Realisasi</th>
                                    <th class="px-3 py-3">Rencana</th>
                                    <th class="px-3 py-3">Realisasi</th>
                                    <th class="px-3 py-3 text-right">Selisih Jam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($crossYearRealizedRows as $index => $row)
                                    <tr data-testid="diklat-gap-row-cross-year-realized-{{ $index }}">
                                        <td class="px-3 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $row['pegawai'] }}</td>
                                        <td class="px-3 py-3">{{ $row['unit_kerja'] }}</td>
                                        <td class="px-3 py-3">{{ $row['tahun_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ $row['tahun_realisasi'] }}</td>
                                        <td class="px-3 py-3">{{ $row['nama_rencana'] }}</td>
                                        <td class="px-3 py-3">{{ $row['nama_realisasi'] }}</td>
                                        <td class="px-3 py-3 text-right">{{ number_format($row['gap_jam']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-3 py-6 text-center text-gray-400">Tidak ada data realisasi lintas tahun.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-6 text-sm text-gray-500 dark:text-gray-400">
                Pilih tahun rencana untuk menampilkan laporan kesenjangan Diklat pegawai.
            </div>
        @endif
    </div>
</x-app-layout>
