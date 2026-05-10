<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah latihan jabatan
                </h1>
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
                <form action="/kepegawaian/latihan_jabatan/edit_latihan_jabatan/{{ $latihanJabatan->id }}" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="fileLama" value="{{ $latihanJabatan->fileLama }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $latihanJabatan->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_pelatih" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Pelatih <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_pelatih" name="nama_pelatih"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama pelatih" required
                            value="{{ old('nama_pelatih', $latihanJabatan->nama_pelatih) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_latihan" class="w-1/4 text-sm font-medium text-heading">Tempat dan Waktu
                            Latihan Jabatan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_latihan" name="tempat_latihan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat latihan" required
                                value="{{ old('tempat_latihan', $latihanJabatan->tempat_latihan) }}" />

                            <input type="date" id="waktu_latihan" name="waktu_latihan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('waktu_latihan', $latihanJabatan->waktu_latihan) }}"
                                placeholder="Masukan tanggal latihan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="tahun_latihan" class="block mb-2.5 text-sm font-medium text-heading">Tahun
                            Latihan <span class="text-red-500">*</span></label>
                        <input type="text" id="tahun_latihan" name="tahun_latihan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tahun latihan" required
                            value="{{ old('tahun_latihan', $latihanJabatan->tahun_latihan) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="jumlah_jam" class="block mb-2.5 text-sm font-medium text-heading">Jumlah Jam <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jumlah_jam" name="jumlah_jam"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jumlah jam" required
                            value="{{ old('jumlah_jam', $latihanJabatan->jumlah_jam) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="nomor_sertifikat" class="w-1/4 text-sm font-medium text-heading">Nomor dan Tgl
                            Sertifikat <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="nomor_sertifikat" name="nomor_sertifikat"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor sertifikat" required
                                value="{{ old('nomor_sertifikat', $latihanJabatan->nomor_sertifikat) }}" />

                            <input type="date" id="tgl_sertifikat" name="tgl_sertifikat"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tgl_sertifikat', $latihanJabatan->tgl_sertifikat) }}"
                                placeholder="Masukan tanggal sertifikat" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="file_sertifikat" class="block mb-2.5 text-sm font-medium text-heading">File
                            Piagam <span class="text-red-500">*</span></label>
                        <input type="file" id="file_sertifikat" name="file_sertifikat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file sertifikat" required />
                    </div>
                    <button type="button"
                        class="confirm-save text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none"
                        data-title="Konfirmasi Simpan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data latihan jabatan ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/latihan_jabatan"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
