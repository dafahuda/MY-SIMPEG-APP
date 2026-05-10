<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data Seminar</h1>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div class="mb-6">
                <a href="/kepegawaian/seminar/view_form_tambah_seminar"
                    class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path
                            d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Tambah Seminar</span>
                </a>
            </div>

            <form action="/kepegawaian/seminar/cariSeminar" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="cariSeminar" id="cariSeminar" aria-label="Cari Seminar"
                    placeholder="Cari seminar..." value="{{ request('cariSeminar') }}" class="rounded-lg">
                <button type="submit"
                    class="inline-block text-white rounded-lg shadow-lg px-3 py-2 bg-blue-500 hover:bg-blue-700">
                    Save
                </button>
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
                                <div class="font-semibold text-left">Nama Seminar</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Jumlah Jam</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Penyelenggara</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Tingkat</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No. Piagam</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">File Piagam</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($seminar as $data)
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
                                    <div class="text-left">{{ $data->nama_seminar }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">{{ $data->jumlah_jam }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">{{ $data->penyelenggara }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">
                                        {{ $data->tingkat_kegiatan }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->no_piagam }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        <a href="/kepegawaian/seminar/download_piagam/{{ $data->id }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 hover:bg-green-200 text-green-700 transition"
                                            title="Download ST">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="/kepegawaian/seminar/view_form_edit_seminar/{{ $data->id }}"
                                            class="confirm-edit inline-block py-2 px-3 text-white bg-blue-500 hover:bg-blue-700 rounded-lg shadow-lg"
                                            data-title="Konfirmasi Edit"
                                            data-message="Apakah Anda yakin ingin mengedit data seminar ini?">
                                            Edit
                                        </a>
                                        <form action="/kepegawaian/seminar/delete_seminar/{{ $data->id }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="confirm-delete inline-block py-2 px-3 text-white bg-red-500 hover:bg-red-700 rounded-lg shadow-lg"
                                                data-title="Konfirmasi Hapus"
                                                data-message="Apakah Anda yakin ingin menghapus data seminar ini? Data yang dihapus tidak dapat dikembalikan.">
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
                {{ $seminar->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
