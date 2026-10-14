<div class="mb-5">
    <label for="nama_unit" class="block mb-2.5 text-sm font-medium text-heading">Nama Unit
        Kerja <span class="text-red-500">*</span></label>
    <input type="text" id="nama_unit" name="nama_unit"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan nama unit" required
        value="{{ old('nama_unit', $unitKerja->nama_unit ?? '') }}" />
</div>
<div class="mb-5">
    <label for="alamat" class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
            class="text-red-500">*</span> </label>
    <input type="text" id="alamat" name="alamat"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan Alamat" required
        value="{{ old('alamat', $unitKerja->alamat ?? '') }}" />
</div>
