<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Sekretariat</h1>
            </div>



        </div>

        <!-- Cards -->


        <div class="bg-white dark:bg-gray-800 w-full p-6 rounded-xl shadow-md border">

            @if ($sekretariat)
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold">
                        {{ $sekretariat->nama_sekretariat }}
                    </h2>

                    <a href="/manajemen_setup/sekretariat/setup_sekretariat/{{ $sekretariat->id }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm">
                        ⚙️ Setup
                    </a>
                </div>

                <!-- Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                    <div>
                        <p class="text-gray-500">Kabupaten / Kota</p>
                        <p class="font-medium">{{ $sekretariat->kabupaten_kota }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Nama Kabupaten / Kota</p>
                        <p class="font-medium">{{ $sekretariat->nama_kabupaten_kota }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Alamat</p>
                        <p class="font-medium">{{ $sekretariat->alamat }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">No. telp</p>
                        <p class="font-medium">{{ $sekretariat->no_telp }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="font-medium">{{ $sekretariat->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Kepala Dinas</p>
                        <p class="font-medium">{{ $sekretariat->sekretaris }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">NIP Kepala Dinas</p>
                        <p class="font-medium">{{ $sekretariat->nip }}</p>
                    </div>
                </div>
                <div class="mt-8">
                    <p class="text-gray-500 mb-2">Logo</p>

                    <div class="flex items-center gap-4">
                        <img src="{{ asset($sekretariat->gambar_logo) }}"
                            class="w-24 h-24 object-contain border rounded-lg">
                    </div>
                </div>
            @else
                <!-- 🔥 kondisi kalau belum ada data -->
                <div class="text-center py-10">
                    <p class="text-gray-500 mb-4">Data sekretariat belum tersedia</p>

                    <a href="/manajemen_setup/sekretariat/create" class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                        ➕ Tambah Sekretariat
                    </a>
                </div>
            @endif

        </div>


    </div>
</x-app-layout>
