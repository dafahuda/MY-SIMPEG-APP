<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Report</span>
                    <span>/</span>
                    <span>Keadaan Pegawai</span>
                    @if ($selectedUnitKerja)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedUnitKerja->nama_unit }}</span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Report <span class="text-base font-normal text-gray-500 dark:text-gray-400">Keadaan Pegawai</span>
                </h1>
            </div>
            @if ($selectedUnitKerja && $stats)
                <a href="{{ route('report.keadaan_pegawai.print', ['unit_kerja_id' => $selectedUnitKerja->id]) }}"
                    target="_blank"
                    class="btn bg-green-500 hover:bg-green-600 text-white">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                        <path d="M15 10h-2V2H3v8H1a1 1 0 00-1 1v3a1 1 0 001 1h14a1 1 0 001-1v-3a1 1 0 00-1-1zM4 3h8v7H4V3zm8 10H4v-2h8v2zm2 0h-1v-2H3v2H2v-2h1V9h10v2h1v2z"/>
                    </svg>
                    <span class="ml-2">Print</span>
                </a>
            @endif
        </div>

        {{-- Filter --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form method="GET" action="{{ url('/report/keadaan_pegawai') }}"
                class="flex flex-col sm:flex-row items-end gap-3">
                <div class="flex flex-col gap-1 flex-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Pilih OPD / SKPD / Unit Kerja
                    </label>
                    <select name="unit_kerja_id"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">-- Pilih Unit Kerja --</option>
                        @foreach ($unitKerjaList as $uk)
                            <option value="{{ $uk->id }}"
                                {{ request('unit_kerja_id') == $uk->id ? 'selected' : '' }}>
                                {{ $uk->nama_unit }}
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

        @if ($selectedUnitKerja && $stats)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-6">

                {{-- Kop --}}
                <div class="text-center mb-4">
                    <p class="font-bold text-gray-800 dark:text-gray-100 uppercase text-sm tracking-wide">
                        LAPORAN BULANAN KEADAAN PEGAWAI
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 uppercase">
                        DI LINGKUNGAN {{ strtoupper($selectedUnitKerja->nama_unit) }}
                        @if ($instansi)
                            {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
                        @endif
                    </p>
                </div>

                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-4 uppercase">
                    PERIODE : BULAN {{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }} TAHUN {{ $tahun }}
                </p>

                @php
                    $cnt    = $stats['cnt'];
                    $sumGol = $stats['sumGol'];
                    $dash   = fn($v) => $v > 0 ? $v : '-';

                    // Baris data: [key, label]
                    $rows = [
                        // Jumlah Pegawai
                        ['section' => 'JUMLAH PEGAWAI', 'items' => [
                            ['key' => 'laki',      'label' => '- Laki-laki'],
                            ['key' => 'perempuan', 'label' => '- Perempuan'],
                        ]],
                        // Jenjang Pendidikan
                        ['section' => 'JENJANG PENDIDIKAN', 'items' => [
                            ['key' => 'sd',      'label' => '- SD/MI'],
                            ['key' => 'smp',     'label' => '- SMP/MTS'],
                            ['key' => 'sma',     'label' => '- SMK/SMA/MA'],
                            ['key' => 'd2d3',    'label' => '- D2/D3'],
                            ['key' => 'd4s1',    'label' => '- S1/D4'],
                            ['key' => 's2s3',    'label' => '- S2/S3'],
                            ['key' => 'profesi', 'label' => '- PROFESI'],
                        ]],
                    ];
                @endphp

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse border border-gray-300 dark:border-gray-600">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <th rowspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center w-8">NO</th>
                                <th rowspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-left min-w-[160px]">JENIS LAPORAN</th>
                                <th colspan="5" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">GOLONGAN</th>
                                <th colspan="5" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">ESELON</th>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">I</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">II</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">III</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">IV</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-14">JUMLAH</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">II</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">III</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">IV</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-10">V</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center w-14">STAFF</th>
                            </tr>
                            {{-- Nomor kolom --}}
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500">
                                @foreach (range(1, 12) as $n)
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center font-medium">{{ $n }}</td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($rows as $section)
                                {{-- Section header --}}
                                <tr class="bg-gray-50 dark:bg-gray-700/30">
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center font-semibold text-gray-700 dark:text-gray-200">
                                        {{ $no++ }}
                                    </td>
                                    <td colspan="11" class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 font-semibold text-gray-700 dark:text-gray-200 uppercase text-xs">
                                        {{ $section['section'] }}
                                    </td>
                                </tr>
                                {{-- Section rows --}}
                                @foreach ($section['items'] as $item)
                                    @php $r = $cnt[$item['key']]; @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5"></td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-gray-700 dark:text-gray-300">
                                            {{ $item['label'] }}
                                        </td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['I']) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['II']) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['III']) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['IV']) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center font-medium">{{ $dash($sumGol($r)) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['esl_II'] ?? 0) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['esl_III'] ?? 0) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['esl_IV'] ?? 0) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['esl_V'] ?? 0) }}</td>
                                        <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">{{ $dash($r['staff'] ?? 0) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                            {{-- Mutasi Pegawai --}}
                            <tr class="bg-gray-50 dark:bg-gray-700/30">
                                <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $no++ }}
                                </td>
                                <td colspan="11" class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 font-semibold text-gray-700 dark:text-gray-200 uppercase text-xs">
                                    MUTASI PEGAWAI
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                                <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5"></td>
                                <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-gray-700 dark:text-gray-300">- Masuk</td>
                                <td colspan="10" class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">
                                    {{ $stats['mutasi_masuk'] > 0 ? $stats['mutasi_masuk'] : '-' }}
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/20">
                                <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5"></td>
                                <td class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-gray-700 dark:text-gray-300">- Keluar</td>
                                <td colspan="10" class="border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-center">
                                    {{ $stats['mutasi_keluar'] > 0 ? $stats['mutasi_keluar'] : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    Total pegawai: <strong class="text-gray-700 dark:text-gray-200">{{ $stats['total'] }}</strong> orang
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
