<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah data riwayat
                    pendidikan bahasa</h1>
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
                <form action="/riwayat_pendidikan/pendidikan_bahasa/tambah_data_bahasa" method="POST">
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
                        <label for="jenis_bahasa" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Bahasa <span class="text-red-500">*</span></label>
                        <input type="text" id="jenis_bahasa" name="jenis_bahasa"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jenis Bahasa" required />
                    </div>
                    <div class="mb-5">
                        <label for="bahasa" class="block mb-2.5 text-sm font-medium text-heading">Bahasa <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="bahasa" name="bahasa"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Bahasa" required />
                    </div>
                    <div class="mb-5">
                        <label for="kemampuan_bicara" class="block mb-2.5 text-sm font-medium text-heading">Kemampuan
                            Bahasa <span class="text-red-500">*</span></label>
                        <select name="kemampuan_bicara" id="kemampuan_bicara" class="rounded-lg">
                            <option value="Aktif">Aktif</option>
                            <option value="Pasif">Pasif</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>
            <div class="mt-5">
                <a href="/riwayat_pendidikan/pendidikan_bahasa"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
