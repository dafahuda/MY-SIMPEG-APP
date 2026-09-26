<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Kepegawaian</span>
                    <span class="mx-1">/</span>
                    <span>Diklat</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data Diklat</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Lihat realisasi diklat beserta keterkaitannya dengan rencana untuk membedakan linked, out of plan, dan cross year.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
                <div class="space-y-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Badge status membantu membedakan realisasi yang sudah memenuhi rencana, terjadi di luar rencana, atau terealisasi lintas tahun.
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span data-testid="diklat-link-status-badge" data-link-state="linked"
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-300">
                            Linked
                        </span>
                        <span data-testid="diklat-link-status-badge" data-link-state="out_of_plan"
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-300">
                            Out of plan
                        </span>
                        <span data-testid="diklat-link-status-cross_year"
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-300">
                            Cross year
                        </span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    @if ($canMutate)
                        <a href="/kepegawaian/diklat/view_form_tambah_diklat"
                            class="btn bg-indigo-500 hover:bg-indigo-600 text-white shrink-0">
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                            <span class="hidden xs:block ml-2">Tambah Diklat</span>
                        </a>
                    @endif

                    <form action="/kepegawaian/diklat/cariDiklat" enctype="multipart/form-data" method="POST"
                        class="flex flex-col sm:flex-row sm:items-end gap-3">
                        @csrf
                        <div class="flex flex-col gap-1">
                            <label for="cariDiklat" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cari realisasi</label>
                            <input type="text" name="cariDiklat" id="cariDiklat" value="{{ request('cariDiklat') }}"
                                aria-label="Cari Diklat" placeholder="Cari diklat, pegawai, penyelenggara, atau tahun"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-72 px-3 py-2.5">
                        </div>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 text-white rounded-lg shadow-lg px-4 py-2.5 bg-blue-500 hover:bg-blue-700 whitespace-nowrap">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
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
                                <div class="font-semibold text-left">Nama Diklat</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Jumlah Jam</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Penyelenggara</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Tahun</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Rencana</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No. STTPP</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Sertifikat</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Aksi</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($diklat as $data)
                            @php
                                $isLinked = $data->rencanaDiklat !== null;
                                $isCrossYear = $isLinked && (string) $data->tahun !== (string) $data->rencanaDiklat->tahun_rencana;
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
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
                                    <div class="text-left">{{ $data->nama_diklat }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">
                                        {{ $data->jumlah_jam }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->penyelenggara }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->tahun }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center">
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                    @if ($isLinked)
                                        <span data-testid="diklat-link-status-badge" data-link-state="linked"
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-300">
                                            Linked
                                        </span>
                                        @if ($isCrossYear)
                                            <span data-testid="diklat-link-status-cross_year"
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-300">
                                                Cross year
                                            </span>
                                        @endif
                                    @else
                                        <span data-testid="diklat-link-status-badge" data-link-state="out_of_plan"
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-300">
                                            Out of plan
                                        </span>
                                    @endif
                                    </div>

                                    @if ($isLinked)
                                        <div class="mt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                                            {{ $data->rencanaDiklat->nama_diklat_rencana }} · {{ $data->rencanaDiklat->tahun_rencana }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->no_sttpp }}
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        <a href="/kepegawaian/diklat/download_sertifikat_diklat/{{ $data->id }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 hover:bg-green-200 text-green-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    @if ($canMutate)
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="/kepegawaian/diklat/view_form_edit_diklat/{{ $data->id }}"
                                                class="confirm-edit inline-block py-2 px-3 text-white bg-blue-500 hover:bg-blue-700 rounded-lg shadow-lg"
                                                data-title="Edit Diklat"
                                                data-message="Apakah anda yakin mau edit data diklat ini?">Ubah</a>
                                            <form action="/kepegawaian/diklat/delete_data_diklat/{{ $data->id }}"
                                                method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="confirm-delete inline-block py-2 px-3 text-white bg-red-500 hover:bg-red-700 rounded-lg shadow-lg"
                                                    data-title="Konfirmasi Hapus"
                                                    data-message="Apakah Anda yakin ingin menghapus data diklat ini? Data yang dihapus tidak dapat dikembalikan.">Hapus</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
