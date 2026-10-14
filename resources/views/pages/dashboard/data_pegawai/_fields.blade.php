@if ($section === 'nip')
<div class="mb-5">
    <label for="nip" class="block mb-2.5 text-sm font-medium text-heading">NIP <span
            class="text-red-500">*</span></label>
    <input type="text" id="nip" name="nip"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan NIP" required @isset($values['nip']) value="{{ $values['nip'] }}" @endisset />
</div>
<div class="mb-5">
    <label for="nama" class="block mb-2.5 text-sm font-medium text-heading">Nama
        Pegawai <span class="text-red-500">*</span></label>
    <input type="text" id="nama" name="nama"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan nama pegawai" required @isset($values['nama']) value="{{ $values['nama'] }}" @endisset />
</div>
@endif
@if ($section === 'nik')
<div class="mb-5">
    <label for="nik" class="block mb-2.5 text-sm font-medium text-heading">NIK <span
            class="text-red-500">*</span> </label>
    <input type="text" id="nik" name="nik"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan NIK Pegawai" required @isset($values['nik']) value="{{ $values['nik'] }}" @endisset />
</div>
<div class="mb-5">
    <label for="no_hp" class="block mb-2.5 text-sm font-medium text-heading">Nomor
        Telepon <span class="text-red-500">*</span></label>
    <input type="text" id="no_hp" name="no_hp"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan Nomor Telepon" required @isset($values['no_hp']) value="{{ $values['no_hp'] }}" @endisset />
</div>
<div class="mb-5">
    <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email <span
            class="text-red-500">*</span></label>
    <input type="text" id="email" name="email"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan Email" required @isset($values['email']) value="{{ $values['email'] }}" @endisset />
</div>
@endif
@if ($section === 'alamat')
<div class="mb-5">
    <label for="alamat" class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
            class="text-red-500">*</span></label>
    <input type="text" id="alamat" name="alamat"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan Alamat" required @isset($values['alamat']) value="{{ $values['alamat'] }}" @endisset />
</div>
<div class="mb-5">
    <label for="no_npwp" class="block mb-2.5 text-sm font-medium text-heading">No.
        NPWP <span class="text-red-500">*</span></label>
    <input type="text" id="no_npwp" name="no_npwp"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan No. NPWP" required @isset($values['no_npwp']) value="{{ $values['no_npwp'] }}" @endisset />
</div>
<div class="mb-5">
    <label for="no_bpjs" class="block mb-2.5 text-sm font-medium text-heading">No.
        BPJS <span class="text-red-500">*</span></label>
    <input type="text" id="no_bpjs" name="no_bpjs"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan No. BPJS" required @isset($values['no_bpjs']) value="{{ $values['no_bpjs'] }}" @endisset />
</div>
@endif
@if ($section === 'karpeg')
<div class="mb-5">
    <label for="karpeg" class="block mb-2.5 text-sm font-medium text-heading">Karpeg <span
            class="text-red-500">*</span></label>
    <input type="text" id="karpeg" name="karpeg"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan karpeg" required @isset($values['karpeg']) value="{{ $values['karpeg'] }}" @endisset />
</div>
@endif
