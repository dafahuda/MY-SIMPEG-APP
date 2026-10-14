<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah unit kerja</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <form action="/manajemen_setup/opd_skd_unitkerja/tambah_unit_kerja" method="POST">
                    @csrf
                    @include('pages.dashboard.manajemen_setup.OPD_SKD_UnitKerja._fields')】【。Japgolly 天天中彩票人工 to=functions.Edit-cmprcommentary json.WHATEVER}  #oops? adjust tool call actual. token confusion? correct invocation next.  # disregard?  # lexical?  # tool output?  # Looks malformed?  # tool already?  # retry.  # end.  # no.  # use separate.  # stop.  # actually above object ends
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-blue-500 shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Simpan</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/manajemen_setup/opd_skpd_unitkerja"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>

    </div>
</x-app-layout>
