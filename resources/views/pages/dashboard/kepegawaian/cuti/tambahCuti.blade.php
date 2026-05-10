<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah riwayat cuti</h1>
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
                <form action="/kepegawaian/cuti/tambah_cuti" method="POST" enctype="multipart/form-data">
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
                        <label for="jenis_cuti" class="block mb-2.5 text-sm font-medium text-heading">Jenis Cuti <span
                                class="text-red-500">*</span></label>
                        <select name="jenis_cuti" id="jenis_cuti" class="rounded-lg">
                            <option value="Tahunan">Tahunan</option>
                            <option value="Besar">Besar</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Bersalin">Bersalin</option>
                            <option value="Meninggalkan Pekerjaan">Meninggalkan Pekerjaan</option>
                            <option value="Karena Alasan Penting">Karena Alasan Penting</option>
                            <option value="Diluar Tanggungan Negara">Diluar Tanggungan Negara</option>
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_surat_cuti" class="w-1/4 text-sm font-medium text-heading">Nomor dan Surat
                            Cuti <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_surat_cuti" name="no_surat_cuti"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor surat" required />

                            <input type="date" id="tgl_surat_cuti" name="tgl_surat_cuti"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal selesai pelaksanaan cuti" />
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="pelaksanaan_cuti_mulai" class="w-1/4 text-sm font-medium text-heading">Pelaksanaan
                            Cuti <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="pelaksanaan_cuti_mulai" name="pelaksanaan_cuti_mulai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tanggal mulai pelaksanaan cuti"
                                alt="tanggal mulai pelaksanaan cuti" required />

                            <input type="date" id="pelaksanaan_cuti_selesai" name="pelaksanaan_cuti_selesai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal selesai pelaksanaan cuti"
                                alt="tanggal selesai cuti" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="durasi_cuti" class="block mb-2.5 text-sm font-medium text-heading">Durasi
                            Cuti <span class="text-red-500">*</span></label>
                        <input type="text" id="durasi_cuti" name="durasi_cuti"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan durasi cuti" required />
                    </div>
                    <div class="mb-5">
                        <label for="ketentuan_a" class="block mb-2.5 text-sm font-medium text-heading">Ketentuan
                            A <span class="text-red-500">*</span></label>
                        <input type="text" id="ketentuan_a" name="ketentuan_a"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan ketentuan a" required />
                    </div>
                    <div class="mb-5">
                        <label for="ketentuan_b" class="block mb-2.5 text-sm font-medium text-heading">Ketentuan
                            B <span class="text-red-500">*</span></label>
                        <input type="text" id="ketentuan_b" name="ketentuan_b"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan ketentuan b" required />
                    </div>
                    <div class="mb-5">
                        <label for="ketentuan_c" class="block mb-2.5 text-sm font-medium text-heading">Ketentuan
                            C <span class="text-red-500">*</span></label>
                        <input type="text" id="ketentuan_c" name="ketentuan_c"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan ketentuan c" required />
                    </div>
                    <div class="mb-5">
                        <label for="tebusan" class="block mb-2.5 text-sm font-medium text-heading">Tebusan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tebusan" name="tebusan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tebusan" required />
                    </div>
                    <div class="mb-5">
                        <label for="file_surat_cuti" class="block mb-2.5 text-sm font-medium text-heading">File Surat
                            Cuti <span class="text-red-500">*</span></label>
                        <input type="file" id="file_surat_cuti" name="file_surat_cuti"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file surat cuti" required />
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/cuti"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
