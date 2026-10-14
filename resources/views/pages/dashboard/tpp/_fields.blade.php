{{-- Pegawai --}}
<div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
    <label for="pegawai_id"
        class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
        Pegawai <span class="text-red-500">*</span>
    </label>
    <div class="flex-1">
        <select name="pegawai_id" id="pegawai_id"
            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm {{ $tpp ? 'rounded-lg' : 'rounded' }} focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error('pegawai_id') border-red-500 @enderror">
            <option value="">Pilih Pegawai</option>
            @foreach ($pegawai as $item)
                <option value="{{ $item->id }}"
                    {{ old('pegawai_id', $tpp ? $tpp->pegawai_id : null) == $item->id ? 'selected' : '' }}>
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
            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error('periode') border-red-500 @enderror">
            @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                <option value="{{ $bulan }}"
                    {{ old('periode', $tpp ? $tpp->periode : null) == $bulan ? 'selected' : '' }}>{{ $bulan }}
                </option>
            @endforeach
        </select>
        <select name="tahun" id="tahun"
            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error('tahun') border-red-500 @enderror">
            @for ($y = date('Y'); $y >= 2020; $y--)
                <option value="{{ $y }}"
                    {{ old('tahun', $tpp ? $tpp->tahun : date('Y')) == $y ? 'selected' : '' }}>{{ $y }}
                </option>
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
            value="{{ old('jml_hari_kerja', $tpp ? $tpp->jml_hari_kerja : 0) }}" min="0"
            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error('jml_hari_kerja') border-red-500 @enderror" />
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
            value="{{ old('tidak_ada_produktifitas', $tpp ? $tpp->tidak_ada_produktifitas : 0) }}" min="0"
            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error('tidak_ada_produktifitas') border-red-500 @enderror" />
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
                value="{{ old($field['name'], $tpp ? $tpp->{$field['name']} : 0) }}" min="0"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error($field['name']) border-red-500 @enderror" />
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
                value="{{ old($field['name'], $tpp ? $tpp->{$field['name']} : 0) }}" min="0"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error($field['name']) border-red-500 @enderror" />
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
            value="{{ old('tidak_masuk_kerja', $tpp ? $tpp->tidak_masuk_kerja : 0) }}" min="0"
            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 @error('tidak_masuk_kerja') border-red-500 @enderror" />
        <span class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">* Hari</span>
    </div>
</div>
 қ.