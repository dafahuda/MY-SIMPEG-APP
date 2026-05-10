<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit seminar</h1>
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
                <form action="/kepegawaian/seminar/edit_seminar/{{ $seminar->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $seminar->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_seminar" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Seminar <span class="text-red-500">*</span></label>

                        <textarea id="nama_seminar" name="nama_seminar" rows="4" cols="50" placeholder="Masukan nama seminar"
                            class="rounded-lg">{{ old('nama_seminar', $seminar->nama_seminar) }}</textarea>
                    </div>
                    <div class="mb-5">
                        <label for="tingkat_kegiatan" class="block mb-2.5 text-sm font-medium text-heading">Tingkat
                            Kegiatan <span class="text-red-500">*</span></label>
                        <select name="tingkat_kegiatan" id="tingkat_kegiatan" class="rounded-lg">
                            <option value="Lokal" {{ $seminar->tingkat_kegiatan == 'Lokal' ? 'selected' : '' }}>Lokal
                            </option>
                            <option value="Regional" {{ $seminar->tingkat_kegiatan == 'Regional' ? 'selected' : '' }}>
                                Regional</option>
                            <option value="Nasional" {{ $seminar->tingkat_kegiatan == 'Nasional' ? 'selected' : '' }}>
                                Nasional</option>
                            <option value="Internasional"
                                {{ $seminar->tingkat_kegiatan == 'Internasional' ? 'selected' : '' }}>Internasional
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_seminar" class="w-1/4 text-sm font-medium text-heading">Tempat dan Tgl
                            Seminar <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_seminar" name="tempat_seminar"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat seminar" required
                                value="{{ old('tempat_seminar', $seminar->tempat_seminar) }}" />

                            <input type="date" id="tgl_seminar" name="tgl_seminar"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal seminar"
                                value="{{ old('tgl_seminar', $seminar->tgl_seminar) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="penyelenggara" class="block mb-2.5 text-sm font-medium text-heading">Penyelenggara
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="penyelenggara" name="penyelenggara"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan penyelenggara" required
                            value="{{ old('penyelenggara', $seminar->penyelenggara) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="jumlah_jam" class="block mb-2.5 text-sm font-medium text-heading">Jumlah Jam <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jumlah_jam" name="jumlah_jam"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jumlah jam" required
                            value="{{ old('jumlah_jam', $seminar->jumlah_jam) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_piagam" class="w-1/4 text-sm font-medium text-heading">Nomor dan Tanggal
                            Piagam <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_piagam" name="no_piagam"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor piagam" required
                                value="{{ old('no_piagam', $seminar->no_piagam) }}" />

                            <input type="date" id="tgl_piagam" name="tgl_piagam"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal piagam"
                                value="{{ old('tgl_piagam', $seminar->tgl_piagam) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="file_piagam" class="block mb-2.5 text-sm font-medium text-heading">File
                            Piagam <span class="text-red-500">*</span></label>
                        <input type="file" id="file_piagam" name="file_piagam"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file piagam" />
                    </div>
                    <button type="button"
                        class="confirm-save text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none"
                        data-title="Konfirmasi Simpan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data seminar ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/seminar"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
