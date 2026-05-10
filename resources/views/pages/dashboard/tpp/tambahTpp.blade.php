<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Riwayat</span>
                    <span class="mx-1">/</span>
                    <a href="{{ route('tpp.index') }}" class="hover:underline">Tambahan Penghasilan Pegawai / TPP</a>
                    <span class="mx-1">/</span>
                    <span>Insert</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Tambah Data TPP</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 p-6">

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Form master data Perhitungan Pembayaran TPP</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tpp.store') }}" method="POST">
                @csrf

                {{-- Pegawai --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label for="pegawai_id"
                        class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Pegawai <span class="text-red-500">*</span>
                    </label>
                    <div class="flex-1">
                        <select name="pegawai_id" id="pegawai_id"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('pegawai_id') border-red-500 @enderror">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Periode --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Periode <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 gap-3">
                        <select name="periode" id="periode"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('periode') border-red-500 @enderror">
                            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                <option value="{{ $bulan }}" {{ old('periode') == $bulan ? 'selected' : '' }}>
                                    {{ $bulan }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" id="tahun"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('tahun') border-red-500 @enderror">
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}"
                                    {{ old('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Jumlah Hari Kerja --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label for="jml_hari_kerja"
                        class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Jml. Hari Kerja <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 items-center gap-2">
                        <input type="number" id="jml_hari_kerja" name="jml_hari_kerja"
                            value="{{ old('jml_hari_kerja', 0) }}" min="0"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('jml_hari_kerja') border-red-500 @enderror" />
                        <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">* Hari</span>
                    </div>
                </div>

                {{-- Section: Produktifitas Kerja --}}
                <div class="mb-2 mt-6">
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 underline">Produktifitas Kerja</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label for="tidak_ada_produktifitas"
                        class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Tidak Ada Produktifitas Kerja <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 items-center gap-2">
                        <input type="number" id="tidak_ada_produktifitas" name="tidak_ada_produktifitas"
                            value="{{ old('tidak_ada_produktifitas', 0) }}" min="0"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('tidak_ada_produktifitas') border-red-500 @enderror" />
                        <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">* Hari</span>
                    </div>
                </div>

                {{-- Section: Keterlambatan --}}
                <div class="mb-2 mt-6">
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 underline">Keterlambatan</p>
                </div>

                @php
                    $keterlambatan = [
                        ['name' => 'terlambat_1_30', 'label' => 'Terlambat 1 < 31 Menit'],
                        ['name' => 'terlambat_31_60', 'label' => 'Terlambat 31 < 61 Menit'],
                        ['name' => 'terlambat_61_90', 'label' => 'Terlambat 61 < 91 Menit'],
                        ['name' => 'terlambat_91_lebih', 'label' => 'Terlambat > 91 Menit'],
                    ];
                @endphp

                @foreach ($keterlambatan as $field)
                    <div class="flex flex-col sm:flex-row sm:items-center mb-4 gap-2">
                        <label for="{{ $field['name'] }}"
                            class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                            {{ $field['label'] }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-1 items-center gap-2">
                            <input type="number" id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                value="{{ old($field['name'], 0) }}" min="0"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error($field['name']) border-red-500 @enderror" />
                            <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">* Hari</span>
                        </div>
                    </div>
                @endforeach

                {{-- Section: Pulang Sebelum Waktunya --}}
                <div class="mb-2 mt-6">
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 underline">Pulang Sebelum Waktunya</p>
                </div>

                @php
                    $pulangAwal = [
                        ['name' => 'pulang_awal_1_30', 'label' => 'Pulang Awal 1 < 31 Menit'],
                        ['name' => 'pulang_awal_31_60', 'label' => 'Pulang Awal 31 < 61 Menit'],
                        ['name' => 'pulang_awal_61_90', 'label' => 'Pulang Awal 61 < 91 Menit'],
                        ['name' => 'pulang_awal_91_lebih', 'label' => 'Pulang Awal > 91 Menit'],
                    ];
                @endphp

                @foreach ($pulangAwal as $field)
                    <div class="flex flex-col sm:flex-row sm:items-center mb-4 gap-2">
                        <label for="{{ $field['name'] }}"
                            class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                            {{ $field['label'] }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-1 items-center gap-2">
                            <input type="number" id="{{ $field['name'] }}" name="{{ $field['name'] }}"
                                value="{{ old($field['name'], 0) }}" min="0"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error($field['name']) border-red-500 @enderror" />
                            <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">* Hari</span>
                        </div>
                    </div>
                @endforeach

                {{-- Section: Mangkir --}}
                <div class="mb-2 mt-6">
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 underline">Mangkir</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label for="tidak_masuk_kerja"
                        class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Tidak Masuk Kerja <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 items-center gap-2">
                        <input type="number" id="tidak_masuk_kerja" name="tidak_masuk_kerja"
                            value="{{ old('tidak_masuk_kerja', 0) }}" min="0"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('tidak_masuk_kerja') border-red-500 @enderror" />
                        <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">* Hari</span>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path
                                d="M13.5 0H2.5A2.5 2.5 0 000 2.5v11A2.5 2.5 0 002.5 16h11a2.5 2.5 0 002.5-2.5v-11A2.5 2.5 0 0013.5 0zM8 12a3 3 0 110-6 3 3 0 010 6zm4-8H4V2h8v2z" />
                        </svg>
                        Save
                    </button>
                    <a href="{{ route('tpp.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded shadow-sm">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M3.7 7.3L10 1l1.4 1.4L6.8 7l4.6 4.6L10 13 3.7 7.3z" />
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
