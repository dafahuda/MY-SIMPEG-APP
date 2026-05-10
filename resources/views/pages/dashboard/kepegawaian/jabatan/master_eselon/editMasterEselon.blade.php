<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit data master
                    eselon</h1>
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
                <form action="/kepegawaian/master_eselon/edit_master_eselon/{{ $masterEselon->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-5">
                        <label for="nama_eselon" class="block mb-2.5 text-sm font-medium text-heading">Nama Eselon</label>
                        <input type="text" name="nama_eselon" id="nama_eselon" 
                            value="{{ old('nama_jabatan', $masterEselon->nama_eselon) }}"
                            class="border rounded px-3 py-2 w-full">
                    </div>

                    <button type="button" class="text-white bg-blue-500 hover:bg-blue-700 rounded-lg text-sm px-4 py-2 confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan master eselon ini?">
                        Save Changes
                    </button>
                </form>
            </div>
            <div class="mt-5">
                <a href="/kepegawaian/jabatan"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
