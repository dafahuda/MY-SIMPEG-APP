<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Riwayat</span>
                    <span class="mx-1">/</span>
                    <a href="{{ route('tpp.index') }}" class="hover:underline">Tambahan Penghasilan Pegawai / TPP</a>
                    <span class="mx-1">/</span>
                    <span>Ubah</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Edit Data TPP</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Form master data Perhitungan Pembayaran TPP</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tpp.update', $tpp->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('pages.dashboard.tpp._fields', ['tpp' => $tpp])

                {{-- Buttons --}}
                <div class="flex items-center gap-3 mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <button type="button"
                        class="confirm-save inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        data-title="Konfirmasi Simpan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data TPP ini?">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path
                                d="M13.5 0H2.5A2.5 2.5 0 000 2.5v11A2.5 2.5 0 002.5 16h11a2.5 2.5 0 002.5-2.5v-11A2.5 2.5 0 0013.5 0zM8 12a3 3 0 110-6 3 3 0 010 6zm4-8H4V2h8v2z" />
                        </svg>
                        Update
                    </button>
                    <a href="{{ route('tpp.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded shadow-sm">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M3.7 7.3L10 1l1.4 1.4L6.8 7l4.6 4.6L10 13 3.7 7.3z" />
                        </svg>Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
