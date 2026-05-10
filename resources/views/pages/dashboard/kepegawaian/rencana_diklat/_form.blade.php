@php
    $isEdit = isset($rencanaDiklat);
    $formAction = $isEdit ? route('rencana_diklat.update', $rencanaDiklat->id) : route('rencana_diklat.store');
    $selectedStatus = old('status', $rencanaDiklat->status ?? 'planned');
    $tahunSekarang = (int) date('Y');
    $daftarTahun = range($tahunSekarang + 5, $tahunSekarang);
    $kategoriOptions = ['Teknis', 'Manajerial', 'Sosial Kultural', 'Struktural'];
    $prioritasOptions = ['Rendah', 'Sedang', 'Tinggi'];
    $linkedRencana = $isEdit && ($rencanaDiklat->relationLoaded('diklat') ? $rencanaDiklat->diklat !== null : false);
@endphp

<form data-testid="rencana-diklat-form" action="{{ $formAction }}" method="POST" class="space-y-5">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="pegawai_id" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Pegawai <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <select name="pegawai_id" id="pegawai_id"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('pegawai_id') border-red-500 @enderror">
                <option value="">-- Pilih Pegawai --</option>
                @foreach ($pegawaiList as $pegawai)
                    <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $rencanaDiklat->pegawai_id ?? '') == $pegawai->id ? 'selected' : '' }}>
                        {{ $pegawai->nama }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="tahun_rencana" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Tahun <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <select name="tahun_rencana" id="tahun_rencana"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('tahun_rencana') border-red-500 @enderror">
                <option value="">-- Pilih Tahun --</option>
                @foreach ($daftarTahun as $tahun)
                    <option value="{{ $tahun }}" {{ old('tahun_rencana', $rencanaDiklat->tahun_rencana ?? date('Y')) == $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="nama_diklat_rencana" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Nama Diklat Rencana <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <input type="text" id="nama_diklat_rencana" name="nama_diklat_rencana"
                value="{{ old('nama_diklat_rencana', $rencanaDiklat->nama_diklat_rencana ?? '') }}"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('nama_diklat_rencana') border-red-500 @enderror" />
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="target_kompetensi" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Target Kompetensi <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <input type="text" id="target_kompetensi" name="target_kompetensi"
                value="{{ old('target_kompetensi', $rencanaDiklat->target_kompetensi ?? '') }}"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('target_kompetensi') border-red-500 @enderror" />
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="kategori_diklat" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Kategori <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <select name="kategori_diklat" id="kategori_diklat"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('kategori_diklat') border-red-500 @enderror">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriOptions as $kategori)
                    <option value="{{ $kategori }}" {{ old('kategori_diklat', $rencanaDiklat->kategori_diklat ?? '') == $kategori ? 'selected' : '' }}>
                        {{ $kategori }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="prioritas" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Prioritas <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <select name="prioritas" id="prioritas"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('prioritas') border-red-500 @enderror">
                <option value="">-- Pilih Prioritas --</option>
                @foreach ($prioritasOptions as $prioritas)
                    <option value="{{ $prioritas }}" {{ old('prioritas', $rencanaDiklat->prioritas ?? '') == $prioritas ? 'selected' : '' }}>
                        {{ $prioritas }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="target_jam" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Target Jam <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <input type="number" id="target_jam" name="target_jam" min="0"
                value="{{ old('target_jam', $rencanaDiklat->target_jam ?? 0) }}"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('target_jam') border-red-500 @enderror" />
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="target_penyelenggara" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Target Penyelenggara <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <input type="text" id="target_penyelenggara" name="target_penyelenggara"
                value="{{ old('target_penyelenggara', $rencanaDiklat->target_penyelenggara ?? '') }}"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('target_penyelenggara') border-red-500 @enderror" />
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
        <label for="alasan_kebutuhan" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0 pt-2">
            Alasan Kebutuhan <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            <textarea id="alasan_kebutuhan" name="alasan_kebutuhan" rows="4"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('alasan_kebutuhan') border-red-500 @enderror">{{ old('alasan_kebutuhan', $rencanaDiklat->alasan_kebutuhan ?? '') }}</textarea>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
        <label for="catatan" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0 pt-2">
            Catatan
        </label>
        <div class="flex-1">
            <textarea id="catatan" name="catatan" rows="4"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('catatan') border-red-500 @enderror">{{ old('catatan', $rencanaDiklat->catatan ?? '') }}</textarea>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="status" class="sm:w-56 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
            Status <span class="text-red-500">*</span>
        </label>
        <div class="flex-1">
            @if ($linkedRencana)
                <input type="hidden" name="status" value="{{ $rencanaDiklat->status }}" />
                <input type="text" value="{{ ucfirst($rencanaDiklat->status) }}"
                    class="bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg block w-full px-3 py-2.5"
                    disabled />
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Status terkunci karena rencana ini sudah terhubung ke realisasi diklat.</p>
            @else
                <select name="status" id="status"
                    class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5 @error('status') border-red-500 @enderror">
                    <option value="draft" {{ $selectedStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="planned" {{ $selectedStatus === 'planned' ? 'selected' : '' }}>Planned</option>
                </select>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3 pt-5 border-t border-gray-200 dark:border-gray-700">
        <button type="submit" name="save_mode" value="draft" data-testid="rencana-diklat-save-draft-button"
            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            Simpan Draft
        </button>
        <button type="submit" name="save_mode" value="planned"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            Simpan Final
        </button>
        <a href="{{ route('rencana_diklat.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded shadow-sm">
            Cancel
        </a>
    </div>
</form>
