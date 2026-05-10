<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Buat data instansi</h1>
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
                <form action="/manajemen_setup/instansi_lembaga/buat_data_instansi" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <label for="nama_instansi_lembaga" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Instansi Lembaga</label>
                        <input type="text" id="nama_instansi_lembaga" name="nama_instansi_lembaga"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama instansi lembaga" required />
                    </div>
                    <div class="mb-5">
                        <label for="kabupaten_kota" class="block mb-2.5 text-sm font-medium text-heading">Kabupaten /
                            Kota</label>
                        <select name="kabupaten_kota" id="kabupaten_kota">
                            <option value="Kabupaten">
                                Kabupaten
                            </option>
                            <option value="Kota">
                                Kota
                            </option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_kota_kabupaten" class="block mb-2.5 text-sm font-medium text-heading">Nama kota
                            / kabupaten</label>
                        <input type="text" id="nama_kota_kabupaten" name="nama_kota_kabupaten"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama kota / kabupaten" required />
                    </div>
                    <div class="mb-5">
                        <label for="alamat" class="block mb-2.5 text-sm font-medium text-heading">Alamat</label>

                        <textarea id="alamat" name="alamat" rows="4" cols="50"></textarea>
                    </div>
                    <div class="mb-5">
                        <label for="no_telp" class="block mb-2.5 text-sm font-medium text-heading">Nomor
                            Telepon</label>
                        <input type="text" id="no_telp" name="no_telp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nomor telepon" required />
                    </div>
                    <div class="mb-5">
                        <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email Instansi
                            lembaga</label>
                        <input type="text" id="email" name="email"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan email instansi lembaga" required />
                    </div>
                    <div class="mb-5">
                        <label for="kepala_dinas" class="block mb-2.5 text-sm font-medium text-heading">Kepala
                            Dinas</label>
                        <input type="text" id="kepala_dinas" name="kepala_dinas"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan kepala dinas" required />
                    </div>
                    <div class="mb-5">
                        <label for="nip" class="block mb-2.5 text-sm font-medium text-heading">NIP Kepala
                            Dinas</label>
                        <input type="text" id="nip" name="nip"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nip kepala dinas" required />
                    </div>
                    <div class="mb-5">
                        <label for="gambar_logo" class="block mb-2.5 text-sm font-medium text-heading">Foto
                            Logo Instansi</label>
                        <input type="file" id="gambar_logo" name="gambar_logo"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Gambar logo instansi" required />
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/manajemen_setup/instansi_lembaga"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
