<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah data riwayat
                    pendidikan sekolah</h1>
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
                <form action="/riwayat_pendidikan/sekolah/tambah_pendidikan_sekolah" method="POST">
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
                        <label for="jenjang_pendidikan" class="block mb-2.5 text-sm font-medium text-heading">Jenjang
                            Pendidikan <span class="text-red-500">*</span></label>
                        <select name="jenjang_pendidikan" id="jenjang_pendidikan" class="rounded-lg">
                            <option value="SD">SD</option>
                            <option value="MI">MI</option>
                            <option value="SMP">SMP</option>
                            <option value="MTS">MTS</option>
                            <option value="SMK">SMK</option>
                            <option value="SMA">SMA</option>
                            <option value="MA">MA</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="Profesi">Profesi</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_sekolah_universitas" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Sekolah / Universitas <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_sekolah_universitas" name="nama_sekolah_universitas"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan riwayat jenjang pendidikan" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_ijazah" class="w-1/4 text-sm font-medium text-heading">Nomor dan Tanggal
                            Ijazah <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_ijazah" name="no_ijazah"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor Ijazah" required />

                            <input type="date" id="tgl_ijazah" name="tgl_ijazah"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="lokasi" class="block mb-2.5 text-sm font-medium text-heading">Lokasi <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="lokasi" name="lokasi"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Lokasi Sekolah / Universitas" required />
                    </div>
                    <div class="mb-5">
                        <label for="jurusan" class="block mb-2.5 text-sm font-medium text-heading">Jurusan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jurusan" name="jurusan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Jurusan atau Prodi" required />
                    </div>
                    <div class="mb-5">
                        <label for="nama_kepsek_rektor" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Kepsek / Rektor <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_kepsek_rektor" name="nama_kepsek_rektor"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nama Kepsek / Rektor" required />
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/riwayat_pendidikan/sekolah"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
