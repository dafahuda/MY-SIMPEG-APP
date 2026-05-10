<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data Riwayat Pendidikan
                    Sekolah</h1>
            </div>

        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div class="mb-6">
                <a href="/riwayat_pendidikan/sekolah/view_form_tambah_pendidikan_sekolah"
                    class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path
                            d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Tambah Riwayat Pendidikan Sekolah</span>
                </a>
            </div>

            <form action="/riwayat_pendidikan/sekolah/cariPendidikanSekolah" enctype="multipart/form-data"
                method="POST">
                @csrf
                <input type="text" class="rounded-lg" name="cariPendidikanSekolah"
                    placeholder="Cari Riwayat pendidikan sekolah..." aria-label="Cari Riwayat Pendidikan"
                    value="{{ request('cariPendidikanSekolah') }}">
                <button type="submit"
                    class="inline-block text-white rounded-lg shadow-lg px-3 py-2 bg-blue-500 hover:bg-blue-700">Cari</button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead
                        class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50 border-t border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Pegawai</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Jenjang</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Nama Sekolah</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Lokasi</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No. Ijazah</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Tgl. Ijazah</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayatPendidikanSekolah as $data)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $data->id }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->pegawai->nama ?? 'Tidak ada nama pegawai' }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">{{ $data->jenjang_pendidikan }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">
                                        {{ $data->nama_sekolah_universitas }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->lokasi }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->no_ijazah }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->tgl_ijazah }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="/riwayat_pendidikan/sekolah/view_form_edit_pendidikan_sekolah/{{ $data->id }}"
                                            class="inline-block py-2 px-3 text-white bg-blue-500 hover:bg-blue-700 rounded-lg shadow-lg confirm-edit"
                                            data-title="Edit Pendidikan Sekolah"
                                            data-message="Anda akan membuka form edit data pendidikan sekolah ini. Lanjutkan?">
                                            Edit
                                        </a>
                                        <form
                                            action="/riwayat_pendidikan/sekolah/delete_pendidikan_sekolah/{{ $data->id }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="inline-block py-2 px-3 text-white bg-red-500 hover:bg-red-700 rounded-lg shadow-lg confirm-delete">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-center">
                {{ $riwayatPendidikanSekolah->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
