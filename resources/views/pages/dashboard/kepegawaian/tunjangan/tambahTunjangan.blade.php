<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah tunjangan</h1>
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
                <form action="/kepegawaian/tunjangan/tambah_tunjangan" method="POST">
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
                        <label for="no_tunjangan" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            tunjangan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_tunjangan" name="no_tunjangan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor tunjangan" required />

                            <input type="date" id="tgl_tunjangan" name="tgl_tunjangan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal tunjangan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_tunjangan_anak" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Tunjangan <span class="text-red-500">*</span></label>
                        <input type="text" id="jenis_tunjangan_anak" name="jenis_tunjangan_anak"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tunjangan anak" required />
                    </div>
                    <div class="mb-5">
                        <label for="terhitung_mulai" class="block mb-2.5 text-sm font-medium text-heading">Terhitung
                            mulai <span class="text-red-500">*</span></label>
                        <input type="date" id="terhitung_mulai" name="terhitung_mulai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Terhitung dari" required />
                    </div>
                    <div class="mb-5">
                        <label for="akta_perkawinan_dari" class="block mb-2.5 text-sm font-medium text-heading">Akta
                            perkawinan dari <span class="text-red-500">*</span></label>
                        <input type="text" id="akta_perkawinan_dari" name="akta_perkawinan_dari"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Akta Perkawinan dari" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_akta_perkawinan" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            akta perkawinan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_akta_perkawinan" name="no_akta_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor akta perkawinan" required />

                            <input type="date" id="tgl_akta_perkawinan" name="tgl_akta_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal akta perkawinan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="akta_kelahiran_dari" class="block mb-2.5 text-sm font-medium text-heading">Akta
                            kelahiran dari <span class="text-red-500">*</span></label>
                        <input type="text" id="akta_kelahiran_dari" name="akta_kelahiran_dari"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Akta kelahiran dari" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_akta_kelahiran" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            akta kelahiran <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_akta_kelahiran" name="no_akta_kelahiran"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor akta kelahiran" required />

                            <input type="date" id="tgl_akta_kelahiran" name="tgl_akta_kelahiran"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal akta kelahiran" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="tebusan" class="block mb-2.5 text-sm font-medium text-heading">Tebusan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tebusan" name="tebusan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tebusan" required />
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>
            <div class="mt-5">
                <a href="/kepegawaian/tunjangan"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
