<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit mutasi</h1>
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
                <form action="/kepegawaian/mutasi/edit_mutasi/{{ $mutasi->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="fileLama" value="{{ $mutasi->file_sk_mutasi }}">
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $mutasi->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_mutasi" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Mutasi <span class="text-red-500">*</span></label>
                        <select name="jenis_mutasi" id="jenis_mutasi" class="rounded-lg">
                            <option value="Masuk" {{ $mutasi->jenis_mutasi == 'Masuk' ? 'selected' : '' }}>Masuk
                            </option>
                            <option value="Keluar" {{ $mutasi->jenis_mutasi == 'Keluar' ? 'selected' : '' }}>Keluar
                            </option>
                            <option value="Pindah Antar Instansi"
                                {{ $mutasi->jenis_mutasi == 'Pintar Antar Instansi' ? 'selected' : '' }}>Berat</option>
                            <option value="Pensiun" {{ $mutasi->jenis_mutasi == 'Pensiun' ? 'selected' : '' }}>Pensiun
                            </option>
                            <option value="Wafat" {{ $mutasi->jenis_mutasi == 'Wafat' ? 'selected' : '' }}>Wafat
                            </option>
                            <option value="Kenaikan Pangkat"
                                {{ $mutasi->jenis_mutasi == 'Kenaikan Pangkat' ? 'selected' : '' }}>Kenaikan Pangkat
                            </option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="instansi_tujuan" class="block mb-2.5 text-sm font-medium text-heading">Instansi
                            Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" id="instansi_tujuan" name="instansi_tujuan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan instansi tujuan" required
                            value="{{ old('instansi_tujuan', $mutasi->instansi_tujuan) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk_mutasi" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            SK mutasi <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk_mutasi" name="no_sk_mutasi"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor SK" required
                                value="{{ old('no_sk_mutasi', $mutasi->no_sk_mutasi) }}" />

                            <input type="date" id="tgl_sk_mutasi" name="tgl_sk_mutasi"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal SK mutasi"
                                value="{{ old('tgl_sk_mutasi', $mutasi->tgl_sk_mutasi) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="file_sk_mutasi" class="block mb-2.5 text-sm font-medium text-heading">File SK
                            Mutasi <span class="text-red-500">*</span></label>
                        <input type="file" id="file_sk_mutasi" name="file_sk_mutasi"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file surat keterangan mutasi" />
                    </div>
                    <button type="button"
                        class="confirm-save text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none"
                        data-title="Konfirmasi Simpan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data mutasi ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/mutasi"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
