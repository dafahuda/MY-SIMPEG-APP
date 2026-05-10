<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah surat izin kawin
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
                <form action="/kepegawaian/izin_kawin/tambah_izin_kawin" method="POST">
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
                    <div class="flex items-center mb-5">
                        <label for="no_surat_izin_perkawinan" class="w-1/4 text-sm font-medium text-heading">Nomor dan
                            tgl
                            izin perkawinan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_surat_izin_perkawinan" name="no_surat_izin_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor surat izin perkawinan" required />

                            <input type="date" id="tgl_izin_surat_perkawinan" name="tgl_izin_surat_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal surat izin perkawinan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="kebangsaan_pegawai" class="block mb-2.5 text-sm font-medium text-heading">Kebangsaan
                            Pegawai <span class="text-red-500">*</span></label>
                        <input type="text" id="kebangsaan_pegawai" name="kebangsaan_pegawai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan kebangsaan pegawai" required />
                    </div>
                    <div class="mb-5">
                        <label for="nama_wali_bapak_pegawai" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Wali (Bapak) Pegawai <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_wali_bapak_pegawai" name="nama_wali_bapak_pegawai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama wali (bapak) pegawai" required />
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan_wali_bapak_pegawai"
                            class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan_wali_bapak_pegawai" name="pekerjaan_wali_bapak_pegawai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pekerjaan wali (bapak) pegawai" required />
                    </div>
                    <div class="mb-5">
                        <label for="alamat_wali_bapak" class="block mb-2.5 text-sm font-medium text-heading">Alamat
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="alamat_wali_bapak" name="alamat_wali_bapak"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan alamat wali (bapak) pegawai" required />
                    </div>
                    <div class="mb-5">
                        <label for="nama_wali_ibu_pegawai" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Wali (Ibu) Pegawai <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_wali_ibu_pegawai" name="nama_wali_ibu_pegawai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama wali (ibu) pegawai" required />
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan_wali_ibu_pegawai"
                            class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan_wali_ibu_pegawai" name="pekerjaan_wali_ibu_pegawai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pekerjaan wali (ibu) pegawai" required />
                    </div>
                    <div class="mb-5">
                        <label for="alamat_wali_ibu_pegawai"
                            class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="alamat_wali_ibu_pegawai" name="alamat_wali_ibu_pegawai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan alamat wali (ibu) pegawai" required />
                    </div>
                    <div class="mt-5">
                        <h2 class="text-center font-bold">UNTUK MELAKSANAKAN PERKAWINAN DENGAN</h2>
                    </div>
                    <div class="mb-5">
                        <label for="nama_calon_suami_istri" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_calon_suami_istri" name="nama_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama calon suami / istri" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_lahir_calon_suami_istri"
                            class="w-1/4 text-sm font-medium text-heading">Tempat dan tgl
                            lahir <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_lahir_calon_suami_istri"
                                name="tempat_lahir_calon_suami_istri"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat lahir calon suami / istri" required />

                            <input type="date" id="tgl_lahir_calon_suami_istri" name="tgl_lahir_calon_suami_istri"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal lahir calon suami / istri" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan_calon_suami_istri" name="pekerjaan_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pekerjaan calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="nip_nik_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">NIK / NIP <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nip_nik_calon_suami_istri" name="nip_nik_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nip atau nik calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="pangkat_golongan_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Pangkat / Golongan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pangkat_golongan_calon_suami_istri"
                            name="pangkat_golongan_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan golongan calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="jabatan_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Jabatan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jabatan_calon_suami_istri" name="jabatan_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jabatan calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="instansi_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Instansi <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="instansi_calon_suami_istri" name="instansi_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan instansi calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="kebangsaan_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Kebangsaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="kebangsaan_calon_suami_istri" name="kebangsaan_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan kebangsaan calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="agama_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Agama <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="agama_calon_suami_istri" name="agama_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nip atau nik calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="alamat_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="alamat_calon_suami_istri" name="alamat_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan alamat calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="nama_wali_bapak_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Nama Wali (Bapak) <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nama_wali_bapak_calon_suami_istri"
                            name="nama_wali_bapak_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan wali (bapak) calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan_wali_bapak_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan_wali_bapak_calon_suami_istri"
                            name="pekerjaan_wali_bapak_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pekerjaan wali (bapak) calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="alamat_wali_bapak_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="alamat_wali_bapak_calon_suami_istri"
                            name="alamat_wali_bapak_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan alamat wali (bapak) calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="nama_wali_ibu_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Nama Wali (Ibu) <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nama_wali_ibu_calon_suami_istri"
                            name="nama_wali_ibu_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan wali (ibu) calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan_wali_ibu_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan_wali_ibu_calon_suami_istri"
                            name="pekerjaan_wali_ibu_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pekerjaan wali (ibu) calon suami / istri" required />
                    </div>
                    <div class="mb-5">
                        <label for="alamat_wali_ibu_calon_suami_istri"
                            class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="alamat_wali_ibu_calon_suami_istri"
                            name="alamat_wali_ibu_calon_suami_istri"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan alamat wali (ibu) calon suami / istri" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_perkawinan" class="w-1/4 text-sm font-medium text-heading">Tempat dan
                            tanggal perkawinan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_perkawinan" name="tempat_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat perkawinan" required />

                            <input type="date" id="tgl_perkawinan" name="tgl_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal perkawinan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="tgl_ditetapkan_perkawinan"
                            class="block mb-2.5 text-sm font-medium text-heading">Tgl Ditetapkan perkawinan <span
                                class="text-red-500">*</span></label>
                        <input type="date" id="tgl_ditetapkan_perkawinan" name="tgl_ditetapkan_perkawinan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tanggal ditetapkan perkawinan" required />
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/izin_kawin"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
