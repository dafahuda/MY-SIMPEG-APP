<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah data riwayat
                    keluarga Orang Tua Pegawai</h1>
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
                <form action="/riwayat_keluarga/orang_tua/tambah_data_orang_tua" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai -- </option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nik" class="block mb-2.5 text-sm font-medium text-heading">No KTP / NIK <span
                                class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nik" name="nik"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nik Orang Tua" required />
                    </div>
                    <div class="mb-5">
                        <label for="nama" class="block mb-2.5 text-sm font-medium text-heading">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama orang tua" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_lahir" class="w-1/4 text-sm font-medium text-heading">Tempat, Tanggal
                            Lahir <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_lahir" name="tempat_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat lahir" required />

                            <input type="date" id="tgl_lahir" name="tgl_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_kelamin" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="rounded-lg">
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="pendidikan" class="block mb-2.5 text-sm font-medium text-heading">Pendidikan <span
                                class="text-red-500">*</span></label>
                        <select name="pendidikan" id="pendidikan" class="rounded-lg">
                            <option value="SD">SD</option>
                            <option value="SLTP">SLTP</option>
                            <option value="SLTA">SLTA</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan" class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan" name="pekerjaan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Pekerjaan Orang Tua" required />
                    </div>
                    <div class="mb-5">
                        <label for="status_hubungan" class="block mb-2.5 text-sm font-medium text-heading">Status
                            Hubungan <span class="text-red-500">*</span></label>
                        <select name="status_hubungan" id="status_hubungan" class="rounded-lg">
                            <option value="Ayah Kandung">Ayah Kandung</option>
                            <option value="Ibu Kandung">Ibu Kandung</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/riwayat_keluarga/orang_tua"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>

    </div>
</x-app-layout>
