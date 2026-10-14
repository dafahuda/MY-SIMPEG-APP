<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit unit kerja</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <form action="/manajemen_setup/opd_skd_unitkerja/edit_unit_kerja/{{ $unitKerja->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('pages.dashboard.manajemen_setup.OPD_SKD_UnitKerja._fields')
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-blue-500 shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data unit kerja ini?">Simpan</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/manajemen_setup/opd_skpd_unitkerja"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>

    </div>
</x-app-layout>
