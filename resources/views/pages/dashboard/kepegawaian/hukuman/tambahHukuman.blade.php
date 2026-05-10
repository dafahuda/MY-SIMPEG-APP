<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah hukuman</h1>
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
                <form action="/kepegawaian/hukuman/tambah_data_hukuman" method="POST" enctype="multipart/form-data">
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
                        <label for="pelanggaran_yg_dilakukan"
                            class="block mb-2.5 text-sm font-medium text-heading">Pelanggaran yang dilakukan <span
                                class="text-red-500">*</span></label>

                        <textarea id="pelanggaran_yg_dilakukan" name="pelanggaran_yg_dilakukan" rows="4" cols="50"
                            placeholder="Masukan pelanggaran yang dilakukan" class="rounded-lg"></textarea>
                    </div>
                    <div class="mb-5">
                        <label for="tingkat_hukuman" class="block mb-2.5 text-sm font-medium text-heading">Tingkat
                            Hukuman <span class="text-red-500">*</span></label>
                        <select name="tingkat_hukuman" id="tingkat_hukuman" class="rounded-lg">
                            <option value="Ringan">Ringan</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Berat">Berat</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_hukuman" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Hukuman <span class="text-red-500">*</span></label>
                        <select name="jenis_hukuman" id="jenis_hukuman" class="rounded-lg">
                            <option value="Teguran Lisan">Teguran Lisan</option>
                            <option value="Teguran Tertulis">Teguran Tertulis</option>
                            <option value="Tunda Kenaikan Berkala">Tunda Kenaikan Berkala</option>
                            <option value="Tunda Kenaikan Pangkat">Tunda Kenaikan Pangkat</option>
                            <option value="Pemberhentian">Pemberhentian</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="isi_teguran" class="block mb-2.5 text-sm font-medium text-heading">Isi
                            teguran <span class="text-red-500">*</span></label>

                        <textarea id="isi_teguran" name="isi_teguran" rows="4" cols="50" class="rounded-lg"></textarea>
                    </div>
                    <div class="mb-5">
                        <label for="pejabat_pengesahan_sk_hukuman"
                            class="block mb-2.5 text-sm font-medium text-heading">Pejabat Pengesahan Hukuman <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pejabat_pengesahan_sk_hukuman" name="pejabat_pengesahan_sk_hukuman"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pejabat pengesahan hukuman" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            pengesahan SK <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk" name="no_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor SK" required />

                            <input type="date" id="tgl_pengesahan_sk" name="tgl_pengesahan_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal SK" />
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tmt_hukuman_mulai" class="w-1/4 text-sm font-medium text-heading">TMT
                            Hukuman <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="tmt_hukuman_mulai" name="tmt_hukuman_mulai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Dari" required />

                            <input type="date" id="tmt_hukuman_pemulihan" name="tmt_hukuman_pemulihan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Sampai" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="pejabat_pemulihan_hukuman"
                            class="block mb-2.5 text-sm font-medium text-heading">Pejabat Pemulihan Hukuman <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pejabat_pemulihan_hukuman" name="pejabat_pemulihan_hukuman"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pejabat pemulihan hukuman" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            pemulihan hukuman <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_pemulihan_hukuman" name="no_pemulihan_hukuman"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor pemulihan hukuman" required />

                            <input type="date" id="tgl_pemulihan_hukuman" name="tgl_pemulihan_hukuman"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal pemulihan hukuman" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="file_sk_hukuman" class="block mb-2.5 text-sm font-medium text-heading">File SK
                            Hukuman <span class="text-red-500">*</span></label>
                        <input type="file" id="file_sk_hukuman" name="file_sk_hukuman"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file sk hukuman" required />
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/hukuman"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
