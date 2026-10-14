{{-- Only the fields common to both forms; the form, master-data dialogs and scripts stay in their respective views. --}}
<div class="mb-5">
    <label for="jenis_jabatan" class="block mb-2.5 text-sm font-medium text-heading">Jenis
        Jabatan <span class="text-red-500">*</span></label>
    <select name="jenis_jabatan" id="jenis_jabatan" class="rounded-lg">
        @foreach (['Jabatan Struktural', 'Jabatan Fungsional Tertentu', 'Jabatan Fungsional Umum'] as $jenis)
            <option value="{{ $jenis }}" @isset($jabatan) {{ $jabatan->jenis_jabatan == $jenis ? 'selected' : '' }} @endisset>{{ $jenis }}</option>
        @endforeach
    </select>
</div>
<div class="flex items-center mb-5">
    <label for="tmt_jabatan_mulai" class="w-1/4 text-sm font-medium text-heading">TMT
        Jabatan <span class="text-red-500">*</span></label>
    <div class="flex w-3/4 gap-4">
        <input type="date" id="tmt_jabatan_mulai" name="tmt_jabatan_mulai"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
            placeholder="Dari" required @isset($jabatan) value="{{ old('tmt_jabatan_mulai', $jabatan->tmt_jabatan_mulai) }}" @endisset />

        <input type="date" id="tmt_jabatan_selesai" name="tmt_jabatan_selesai"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
            required @isset($jabatan) value="{{ old('tmt_jabatan_selesai', $jabatan->tmt_jabatan_selesai) }}" @endisset />
    </div>
</div>
<div class="mb-5">
    <label for="periode" class="block mb-2.5 text-sm font-medium text-heading">Periode <span
            class="text-red-500">*</span></label>
    <select name="periode" id="periode" class="rounded-lg">
        @foreach (['-', 'I', 'II', 'Sudah Selesai'] as $periode)
            <option value="{{ $periode }}" @isset($jabatan) {{ $jabatan->periode == $periode ? 'selected' : '' }} @endisset>{{ $periode == 'Sudah Selesai' && !isset($jabatan) ? 'Sudah Selesa' : $periode }}</option>
        @endforeach
    </select>
</div>
<div class="mb-5">
    <label for="tahun_ke" class="block mb-2.5 text-sm font-medium text-heading">Tahun Ke <span
            class="text-red-500">*</span></label>
    <select name="tahun_ke" id="tahun_ke" class="rounded-lg">
        @foreach (['-', '1', '2', '3', '4', 'Sudah Selesai'] as $tahun)
            <option value="{{ $tahun }}" @isset($jabatan) {{ ($tahun == '-' ? $jabatan->tahun_ke : $jabatan->periode) == $tahun ? 'selected' : '' }} @endisset>{{ $tahun }}</option>
        @endforeach
    </select>
</div>
<div class="flex items-center mb-5">
    <label for="no_sk" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
        SK <span class="text-red-500">*</span></label>
    <div class="flex w-3/4 gap-4">
        <input type="text" id="no_sk" name="no_sk"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
            placeholder="Masukan nomor SK" required @isset($jabatan) value="{{ old('no_sk', $jabatan->no_sk) }}" @endisset />

        <input type="date" id="tgl_sk" name="tgl_sk"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
            required @isset($jabatan) value="{{ old('tgl_sk', $jabatan->tgl_sk) }}" @endisset />
    </div>
</div>
<div class="mb-5">
    <label for="terbit" class="block mb-2.5 text-sm font-medium text-heading">Diterbitkan
        oleh <span class="text-red-500">*</span></label>
    <input type="text" id="terbit" name="terbit"
        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
        placeholder="Diterbikan oleh" required @isset($jabatan) value="{{ old('terbit', $jabatan->terbit) }}" @endisset />
</div>
