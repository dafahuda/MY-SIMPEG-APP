<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form ubah data riwayat
                    keluarga suami / istri</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
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
                <form action="/riwayat_keluarga/suami_istri/edit_data_suami_istri/{{ $riwayatKeluargaSuamiIstri->id }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $riwayatKeluargaSuamiIstri->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="no_ktp_nik" class="block mb-2.5 text-sm font-medium text-heading">No KTP / NIK <span
                                class="text-red-500">*</span>
                        </label>
                        <input type="text" id="no_ktp_nik" name="no_ktp_nik"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nik suami / istri" required
                            value="{{ old('no_ktp_nik', $riwayatKeluargaSuamiIstri->no_ktp_nik) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="nama" class="block mb-2.5 text-sm font-medium text-heading">Nama Suami
                            Istri <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama suami / istri" required
                            value="{{ old('nama', $riwayatKeluargaSuamiIstri->nama) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_lahir" class="w-1/4 text-sm font-medium text-heading">Tempat, Tanggal
                            Lahir <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_lahir" name="tempat_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat lahir" required
                                value="{{ old('tempat_lahir', $riwayatKeluargaSuamiIstri->tempat_lahir) }}" />

                            <input type="date" id="tgl_lahir" name="tgl_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tgl_lahir', $riwayatKeluargaSuamiIstri->tgl_lahir) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="pendidikan" class="block mb-2.5 text-sm font-medium text-heading">Pendidikan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pendidikan" name="pendidikan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Pendidikan terakhir Suami / Istri" required
                            value="{{ old('pendidikan', $riwayatKeluargaSuamiIstri->pendidikan) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan" class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan" name="pekerjaan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Pekerjaan Suami / Istri" required
                            value="{{ old('pekerjaan', $riwayatKeluargaSuamiIstri->pekerjaan) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="status_hubungan" class="block mb-2.5 text-sm font-medium text-heading">Status
                            Hubungan <span class="text-red-500">*</span></label>
                        <select name="status_hubungan" id="status_hubungan" class="rounded-lg">
                            <option value="Suami"
                                {{ $riwayatKeluargaSuamiIstri->status_hubungan == 'Suami' ? 'selected' : '' }}>Suami
                            </option>
                            <option value="Istri"
                                {{ $riwayatKeluargaSuamiIstri->status_hubungan == 'Istri' ? 'selected' : '' }}>Istri
                            </option>
                        </select>
                    </div>
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data suami / istri ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/riwayat_keluarga/suami_istri"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>

    </div>
</x-app-layout>
