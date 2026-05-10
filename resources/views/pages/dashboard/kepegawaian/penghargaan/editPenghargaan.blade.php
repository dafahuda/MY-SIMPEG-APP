<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit penghargaan
                    pegawai</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 p-5">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 mb-5">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="/kepegawaian/penghargaan/edit_penghargaan/{{ $penghargaan->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="fileLama" id="fileLama"
                        value="{{ $penghargaan->file_sertifikat_penghargaan }}">
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $penghargaan->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_penghargaan" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Penghargaan <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_penghargaan" name="nama_penghargaan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama penghargaan" required
                            value="{{ old('nama_penghargaan', $penghargaan->nama_penghargaan) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="instansi_pemberi" class="block mb-2.5 text-sm font-medium text-heading">Instansi
                            Pemberi <span class="text-red-500">*</span></label>
                        <input type="text" id="instansi_pemberi" name="instansi_pemberi"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan instansi pemberi" required
                            value="{{ old('instansi_pemberi', $penghargaan->instansi_pemberi) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="tingkat_kegiatan" class="block mb-2.5 text-sm font-medium text-heading">Tingkat
                            Kegiatan <span class="text-red-500">*</span></label>
                        <select name="tingkat_kegiatan" id="tingkat_kegiatan" class="rounded-lg">
                            <option value="Lokal" {{ $penghargaan->tingkat_kegiatan == 'Lokal' ? 'selected' : '' }}>
                                Lokal</option>
                            <option value="Regional"
                                {{ $penghargaan->tingkat_kegiatan == 'Regional' ? 'selected' : '' }}>Regional</option>
                            <option value="Nasional"
                                {{ $penghargaan->tingkat_kegiatan == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                            <option value="Internasional"
                                {{ $penghargaan->tingkat_kegiatan == 'Internasional' ? 'selected' : '' }}>Internasional
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_penghargaan" class="w-1/4 text-sm font-medium text-heading">Tempat dan
                            Tanggal <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_penghargaan" name="tempat_penghargaan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat penghargaan" required
                                value="{{ old('tempat_penghargaan', $penghargaan->tempat_penghargaan) }}" />

                            <input type="date" id="tgl_penghargaan" name="tgl_penghargaan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal penghargaan"
                                value="{{ old('tgl_penghargaan', $penghargaan->tgl_penghargaan) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="tahun" class="block mb-2.5 text-sm font-medium text-heading">Tahun <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tahun" name="tahun"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tahun penghargaan" required
                            value="{{ old('tahun', $penghargaan->tahun) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="no_sertifikat" class="block mb-2.5 text-sm font-medium text-heading">Nomor
                            Sertifikat <span class="text-red-500">*</span></label>
                        <input type="text" id="no_sertifikat" name="no_sertifikat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nomor sertifikat" required
                            value="{{ old('no_sertifikat', $penghargaan->no_sertifikat) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="file_sertifikat_penghargaan"
                            class="block mb-2.5 text-sm font-medium text-heading">Sertifikat Penghargaan <span
                                class="text-red-500">*</span></label>
                        <input type="file" id="file_sertifikat_penghargaan" name="file_sertifikat_penghargaan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file sertifikat penghargaan" required />
                    </div>
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data penghargaan ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/penghargaan"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
