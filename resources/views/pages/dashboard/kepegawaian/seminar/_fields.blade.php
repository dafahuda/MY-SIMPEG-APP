<div class="mb-5">
    <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
            class="text-red-500">*</span></label>
    <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
        <option value="">Pilih Pegawai</option>
        @foreach ($pegawai as $item)
            <option value="{{ $item->id }}" @isset($seminar)
                {{ old('pegawai_id', $seminar->pegawai_id) == $item->id ? 'selected' : '' }}
                @endisset>{{ $item->nama }}</option>
        @endforeach
    </select>
</div>
<div class="mb-5">
    <label for="nama_seminar" class="block mb-2.5 text-sm font-medium text-heading">Nama
        Seminar <span class="text-red-500">*</span></label>

    <textarea id="nama_seminar" name="nama_seminar" rows="4" cols="50" placeholder="Masukan nama seminar"
        class="rounded-lg">@isset($seminar){{ old('nama_seminar', $seminar->nama_seminar) }}@endisset</textarea>
</div>
<div class="mb-5">
    <label for="tingkat_kegiatan" class="block mb-2.5 text-sm font-medium text-heading">Tingkat
        Kegiatan <span class="text-red-500">*</span></label>
    <select name="tingkat_kegiatan" id="tingkat_kegiatan" class="rounded-lg">
        <option value="Lokal" @isset($seminar){{ $seminar->tingkat_kegiatan == 'Lokal' ? 'selected' : '' }}@endisset>Lokal</option>
        <option value="Regional" @isset($seminar){{ $seminar->tingkat_kegiatan == 'Regional' ? 'selected' : '' }}@endisset>Regional</option>
        <option value="Nasional" @isset($seminar){{ $seminar->tingkat_kegiatan == 'Nasional' ? 'selected' : '' }}@endisset>Nasional</option>
        <option value="Internasional" @isset($seminar){{ $seminar->tingkat_kegiatan == 'Internasional' ? 'selected' : '' }}@endisset>Internasional</option>
    </select>
</div>
<div class="flex items-center mb-5">
    <label for="tempat_seminar" class="w-1/4 text-sm font-medium text-heading">Tempat dan Tgl
        Seminar <span class="text-red-500">*</span></label>
    <div class="flex w-3/4 gap-4">
        <input type="text" id="tempat_seminar" name="tempat_seminar"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
            placeholder="Masukan tempat seminar" required
            @isset($seminar) value="{{ old('tempat_seminar', $seminar->tempat_seminar) }}" @endisset />

        <input type="date" id="tgl_seminar" name="tgl_seminar"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
            required placeholder="Masukan tanggal seminar"
            @isset($seminar) value="{{ old('tgl_seminar', $seminar->tgl_seminar) }}" @endisset />
    </div>
</div>
<div class="mb-5">
    <label for="penyelenggara" class="block mb-2.5 text-sm font-medium text-heading">Penyelenggara
        <span class="text-red-500">*</span></label>
    <input type="text" id="penyelenggara" name="penyelenggara"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan penyelenggara" required
        @isset($seminar) value="{{ old('penyelenggara', $seminar->penyelenggara) }}" @endisset />
</div>
<div class="mb-5">
    <label for="jumlah_jam" class="block mb-2.5 text-sm font-medium text-heading">Jumlah Jam <span
            class="text-red-500">*</span></label>
    <input type="text" id="jumlah_jam" name="jumlah_jam"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Masukan jumlah jam" required
        @isset($seminar) value="{{ old('jumlah_jam', $seminar->jumlah_jam) }}" @endisset />
</div>
<div class="flex items-center mb-5">
    <label for="no_piagam" class="w-1/4 text-sm font-medium text-heading">Nomor dan {{ $piagamDateLabel }}
        Piagam <span class="text-red-500">*</span></label>
    <div class="flex w-3/4 gap-4">
        <input type="text" id="no_piagam" name="no_piagam"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
            placeholder="Masukan nomor piagam" required
            @isset($seminar) value="{{ old('no_piagam', $seminar->no_piagam) }}" @endisset />

        <input type="date" id="tgl_piagam" name="tgl_piagam"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
            required placeholder="Masukan tanggal piagam"
            @isset($seminar) value="{{ old('tgl_piagam', $seminar->tgl_piagam) }}" @endisset />
    </div>
</div>
