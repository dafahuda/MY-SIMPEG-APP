<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Kepegawaian</span>
                    <span class="mx-1">/</span>
                    <a href="{{ route('rencana_diklat.index') }}" class="hover:underline">Rencana Diklat</a>
                    <span class="mx-1">/</span>
                    <span>Insert</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Tambah Rencana Diklat</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Form master data rencana diklat pegawai</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('pages.dashboard.kepegawaian.rencana_diklat._form')
        </div>
    </div>
</x-app-layout>
