<div class="mb-5">
    <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
            class="text-red-500">*</span></label>
    <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
        <option value="">Pilih Pegawai</option>
        @foreach ($pegawai as $item)
            <option value="{{ $item->id }}" @if ($isEdit) {{ old('pegawai_id', $riwayatPendidikanBahasa->pegawai_id) == $item->id ? 'selected' : '' }} @endif>{{ $item->nama }}</option>
        @endforeach
    </select>
</div>
<div class="mb-5">
    <label for="jenis_bahasa" class="block mb-2.5 text-sm font-medium text-heading">Jenis
        Bahasa <span class="text-red-500">*</span></label>
    <input type="text" id="jenis_bahasa" name="jenis_bahasa"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan jenis Bahasa" required @if ($isEdit) value="{{ old('jenis_bahasa', $riwayatPendidikanBahasa->jenis_bahasa) }}" @endif />
</div>
<div class="mb-5">
    <label for="bahasa" class="block mb-2.5 text-sm font-medium text-heading">Bahasa <span
            class="text-red-500">*</span></label>
    <input type="text" id="bahasa" name="bahasa"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="{{ $isEdit ? 'Masukan jBahasa' : 'Masukan Bahasa' }}" required @if ($isEdit) value="{{ old('bahasa', $riwayatPendidikanBahasa->bahasa) }}" @endif />
</div>
<div class="mb-5">
    <label for="kemampuan_bicara" class="block mb-2.5 text-sm font-medium text-heading">Kemampuan
        Bahasa <span class="text-red-500">*</span></label>
    <select name="kemampuan_bicara" id="kemampuan_bicara" class="rounded-lg">
        <option value="Aktif" @if ($isEdit) {{ $riwayatPendidikanBahasa->kemampuan_bicara == 'Aktif' ? 'selected' : '' }} @endif>Aktif</option>
        <option value="Pasif" @if ($isEdit) {{ $riwayatPendidikanBahasa->kemampuan_bicara == 'Pasif' ? 'selected' : '' }} @endif>Pasif</option>
    </select>
</div>
