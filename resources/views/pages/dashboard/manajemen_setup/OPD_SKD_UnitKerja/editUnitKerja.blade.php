<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit unit kerja</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <form action="/manajemen_setup/opd_skd_unitkerja/edit_unit_kerja/{{ $unitKerja->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="nama_unit" class="block mb-2.5 text-sm font-medium text-heading">Nama Unit
                            Kerja <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_unit" name="nama_unit"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama unit" required
                            value="{{ old('nama_unit', $unitKerja->nama_unit) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="password" class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
                                class="text-red-500">*</span> </label>
                        <input type="text" id="alamat" name="alamat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Alamat" required value="{{ old('alamat', $unitKerja->alamat) }}" />
                    </div>
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data unit kerja ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/manajemen_setup/opd_skpd_unitkerja"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>

    </div>
</x-app-layout>
