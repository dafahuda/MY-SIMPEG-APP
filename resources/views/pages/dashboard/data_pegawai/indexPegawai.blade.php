<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        @php
            $searchTerm = $cariPegawai ?? request('cariPegawai');
            $hasSearch = filled($searchTerm);
        @endphp

        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Kepegawaian</span>
                    <span class="mx-1">/</span>
                    <span>Data Pegawai</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data Pegawai</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Kelola identitas ASN, unit kerja, pangkat, dan biodata pegawai dalam satu daftar resmi.</p>
            </div>

            <a href="/data_pegawai/view_form_tambah_data_pegawai"
                class="btn bg-indigo-500 hover:bg-indigo-600 text-white inline-flex items-center shrink-0 w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                    <path
                        d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span class="ml-2">Tambah Data Pegawai</span>
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Filter Data Pegawai</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cari berdasarkan nama, NIP, golongan, atau unit kerja. Admin hanya melihat pegawai pada unit kerjanya.</p>
            </div>

            <form action="/data_pegawai/cariPegawai" method="POST" class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                @csrf
                <div class="flex flex-col gap-1">
                    <label for="cariPegawai" class="text-sm font-medium text-gray-700 dark:text-gray-300">Kata kunci</label>
                    <input id="cariPegawai" type="text" name="cariPegawai" placeholder="NIP, nama, unit kerja, atau golongan"
                        value="{{ $searchTerm }}" class="form-input w-full">
                    @error('cariPegawai')
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="btn bg-green-500 hover:bg-green-600 text-white justify-center">
                        Terapkan
                    </button>
                    <a href="/data_pegawai/pegawai"
                        class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:border-gray-600 justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 sm:flex sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Daftar Pegawai</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Menampilkan {{ $pegawai->firstItem() ?? 0 }}-{{ $pegawai->lastItem() ?? 0 }} dari {{ $pegawai->total() }} pegawai.
                    </p>
                </div>
                @if ($hasSearch)
                    <span class="mt-3 sm:mt-0 inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                        1 filter aktif: {{ $searchTerm }}
                    </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                    <thead
                        class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Foto</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">NIP</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Nama</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">JK / TTL</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Gol. / Unit Kerja</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse ($pegawai as $data)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $pegawai->firstItem() + $loop->index }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <img src="{{ asset($data->foto) }}" alt="Foto {{ $data->nama }}" class="h-16 w-16 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $data->nip }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $data->nama }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $data->email }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div>{{ ucfirst($data->jenis_kelamin) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $data->tmpt_lahir }}, {{ $data->tgl_lahir }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $data->gol_awal }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $data->unit_kerja->nama_unit ?? 'Tidak ada unit' }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="/data_pegawai/view_form_edit_data_pegawai/{{ $data->id }}"
                                            class="confirm-edit btn-sm bg-indigo-500 hover:bg-indigo-600 text-white"
                                            data-title="Ubah Data Pegawai"
                                            data-message="Anda akan membuka form ubah data pegawai ini. Lanjutkan?">
                                            Ubah
                                        </a>
                                        <form action="/data_pegawai/delete_data_pegawai/{{ $data->id }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="confirm-delete btn-sm bg-red-500 hover:bg-red-600 text-white"
                                                data-title="Konfirmasi Hapus"
                                                data-message="Apakah Anda yakin ingin menghapus data pegawai ini? Data yang dihapus tidak dapat dikembalikan.">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="mx-auto max-w-md">
                                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Tidak ada data pegawai</h3>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            @if ($hasSearch)
                                                Belum ada data pegawai yang sesuai dengan filter. Ubah kata kunci atau reset filter untuk melihat semua data yang bisa Anda akses.
                                            @else
                                                Belum ada data pegawai pada cakupan akses Anda. Tambahkan data pegawai baru bila diperlukan.
                                            @endif
                                        </p>
                                        <div class="mt-4 flex justify-center gap-3">
                                            @if ($hasSearch)
                                                <a href="/data_pegawai/pegawai" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:border-gray-600">Reset Filter</a>
                                            @endif
                                            <a href="/data_pegawai/view_form_tambah_data_pegawai" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Tambah Pegawai</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex justify-center">
            {{ $pegawai->links() }}
        </div>
    </div>
</x-app-layout>
