<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Riwayat</span>
                    <span class="mx-1">/</span>
                    <span>Tambahan Penghasilan Pegawai / TPP</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data TPP</h1>
            </div>
            <div>
                <a href="{{ route('tpp.create') }}"
                    class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="ml-2">Add TPP</span>
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div x-data="tppDetail()" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">

            <!-- Table controls -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 gap-3">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Results <strong class="text-gray-700 dark:text-gray-200">{{ $tpp->total() }}</strong> rows for "Data TPP"
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <span>Show</span>
                        <select class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                        </select>
                        <span>entries</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <label class="text-gray-500 dark:text-gray-400">Search:</label>
                        <form action="/tpp/cariTpp" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="cariTpp" id="cariTpp"
                                value="{{ request('cariTpp') }}"
                                placeholder="Cari nama pegawai atau periode..."
                                class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200" />
                            <button type="submit"
                                class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-sm rounded">
                                Cari
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-body">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-t border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">No</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Periode</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Pegawai</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">Nilai Basic TPP</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">Pengurangan Produktifitas</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">Pengurangan Disiplin</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">TPP Diterima</div>
                            </th>
                            <th class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-center">Action</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($tpp as $index => $data)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-800 dark:text-gray-100">
                                        {{ $tpp->firstItem() + $index }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-gray-800 dark:text-gray-100">
                                        {{ $data->periode }} {{ $data->tahun }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $data->pegawai->nama ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-right text-gray-700 dark:text-gray-300">
                                        {{ number_format($data->nilai_basic_tpp, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-right text-gray-700 dark:text-gray-300">
                                        {{ number_format($data->pengurangan_produktifitas, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-right text-gray-700 dark:text-gray-300">
                                        {{ number_format($data->pengurangan_disiplin, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-right font-semibold text-gray-800 dark:text-gray-100">
                                        {{ number_format($data->tpp_diterima, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-3 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Edit -->
                                        <a href="{{ route('tpp.edit', $data->id) }}"
                                            title="Edit"
                                            class="confirm-edit inline-flex items-center justify-center w-8 h-8 rounded text-white bg-amber-500 hover:bg-amber-600 shadow-sm"
                                            data-title="Konfirmasi Edit"
                                            data-message="Apakah Anda yakin ingin mengedit data TPP ini?">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                                <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z"/>
                                            </svg>
                                        </a>
                                        <!-- Detail / View -->
                                        <button type="button" title="Detail"
                                            @click="openDetail({{ json_encode([
                                                'pegawai'                  => $data->pegawai->nama ?? '-',
                                                'periode'                  => $data->periode . ' ' . $data->tahun,
                                                'jml_hari_kerja'           => $data->jml_hari_kerja,
                                                'tidak_ada_produktifitas'  => $data->tidak_ada_produktifitas,
                                                'terlambat_1_30'           => $data->terlambat_1_30,
                                                'terlambat_31_60'          => $data->terlambat_31_60,
                                                'terlambat_61_90'          => $data->terlambat_61_90,
                                                'terlambat_91_lebih'       => $data->terlambat_91_lebih,
                                                'pulang_awal_1_30'         => $data->pulang_awal_1_30,
                                                'pulang_awal_31_60'        => $data->pulang_awal_31_60,
                                                'pulang_awal_61_90'        => $data->pulang_awal_61_90,
                                                'pulang_awal_91_lebih'     => $data->pulang_awal_91_lebih,
                                                'tidak_masuk_kerja'        => $data->tidak_masuk_kerja,
                                                'nilai_basic_tpp'          => number_format($data->nilai_basic_tpp, 0, ',', '.'),
                                                'pengurangan_produktifitas'=> number_format($data->pengurangan_produktifitas, 0, ',', '.'),
                                                'pengurangan_disiplin'     => number_format($data->pengurangan_disiplin, 0, ',', '.'),
                                                'tpp_diterima'             => number_format($data->tpp_diterima, 0, ',', '.'),
                                            ]) }})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded text-white bg-green-500 hover:bg-green-600 shadow-sm">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                                <path d="M8 3C4.5 3 1.5 5.1.1 8c1.4 2.9 4.4 5 7.9 5s6.5-2.1 7.9-5C14.5 5.1 11.5 3 8 3zm0 8c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-4c-.6 0-1 .4-1 1s.4 1 1 1 1-.4 1-1-.4-1-1-1z"/>
                                            </svg>
                                        </button>
                                        <!-- Delete -->
                                        <form action="{{ route('tpp.destroy', $data->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" title="Hapus"
                                                class="confirm-delete inline-flex items-center justify-center w-8 h-8 rounded text-white bg-red-500 hover:bg-red-600 shadow-sm"
                                                data-title="Konfirmasi Hapus"
                                                data-message="Apakah Anda yakin ingin menghapus data TPP ini? Data yang dihapus tidak dapat dikembalikan.">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                                    <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zM0 3h16v2H0V3zm3 0V1h10v2H3z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-gray-400 dark:text-gray-500">
                                    Belum ada data TPP.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-4 border-t border-gray-200 dark:border-gray-700 gap-3">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Showing {{ $tpp->firstItem() ?? 0 }} to {{ $tpp->lastItem() ?? 0 }} of {{ $tpp->total() }} entries
                </div>
                <div>
                    {{ $tpp->links() }}
                </div>
            </div>

            <!-- Modal Detail TPP -->
            <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="open = false"></div>
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg">
                        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Detail Rincian TPP</h3>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                        </div>
                        <div class="p-5 space-y-3 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-medium">Pegawai</span>
                                <span x-text="detail.pegawai"></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-medium">Periode</span>
                                <span x-text="detail.periode"></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="font-medium">Jml. Hari Kerja</span>
                                <span x-text="detail.jml_hari_kerja + ' Hari'"></span>
                            </div>
                            <p class="font-semibold text-gray-600 dark:text-gray-400 pt-1">Produktifitas Kerja</p>
                            <div class="flex justify-between pl-3">
                                <span>Tidak Ada Produktifitas</span>
                                <span x-text="detail.tidak_ada_produktifitas + ' Hari'"></span>
                            </div>
                            <p class="font-semibold text-gray-600 dark:text-gray-400 pt-1">Keterlambatan</p>
                            <div class="flex justify-between pl-3">
                                <span>Terlambat 1 &lt; 31 Menit</span>
                                <span x-text="detail.terlambat_1_30 + ' Hari'"></span>
                            </div>
                            <div class="flex justify-between pl-3">
                                <span>Terlambat 31 &lt; 61 Menit</span>
                                <span x-text="detail.terlambat_31_60 + ' Hari'"></span>
                            </div>
                            <div class="flex justify-between pl-3">
                                <span>Terlambat 61 &lt; 91 Menit</span>
                                <span x-text="detail.terlambat_61_90 + ' Hari'"></span>
                            </div>
                            <div class="flex justify-between pl-3">
                                <span>Terlambat &gt; 91 Menit</span>
                                <span x-text="detail.terlambat_91_lebih + ' Hari'"></span>
                            </div>
                            <p class="font-semibold text-gray-600 dark:text-gray-400 pt-1">Pulang Sebelum Waktunya</p>
                            <div class="flex justify-between pl-3">
                                <span>Pulang Awal 1 &lt; 31 Menit</span>
                                <span x-text="detail.pulang_awal_1_30 + ' Hari'"></span>
                            </div>
                            <div class="flex justify-between pl-3">
                                <span>Pulang Awal 31 &lt; 61 Menit</span>
                                <span x-text="detail.pulang_awal_31_60 + ' Hari'"></span>
                            </div>
                            <div class="flex justify-between pl-3">
                                <span>Pulang Awal 61 &lt; 91 Menit</span>
                                <span x-text="detail.pulang_awal_61_90 + ' Hari'"></span>
                            </div>
                            <div class="flex justify-between pl-3">
                                <span>Pulang Awal &gt; 91 Menit</span>
                                <span x-text="detail.pulang_awal_91_lebih + ' Hari'"></span>
                            </div>
                            <p class="font-semibold text-gray-600 dark:text-gray-400 pt-1">Mangkir</p>
                            <div class="flex justify-between pl-3 border-b pb-2">
                                <span>Tidak Masuk Kerja</span>
                                <span x-text="detail.tidak_masuk_kerja + ' Hari'"></span>
                            </div>
                            <div class="flex justify-between pt-1">
                                <span class="font-medium">Nilai Basic TPP</span>
                                <span x-text="'Rp ' + detail.nilai_basic_tpp"></span>
                            </div>
                            <div class="flex justify-between text-red-600">
                                <span class="font-medium">Pengurangan Produktifitas</span>
                                <span x-text="'Rp ' + detail.pengurangan_produktifitas"></span>
                            </div>
                            <div class="flex justify-between text-red-600">
                                <span class="font-medium">Pengurangan Disiplin</span>
                                <span x-text="'Rp ' + detail.pengurangan_disiplin"></span>
                            </div>
                            <div class="flex justify-between border-t pt-2 font-bold text-green-600 text-base">
                                <span>TPP Diterima</span>
                                <span x-text="'Rp ' + detail.tpp_diterima"></span>
                            </div>
                        </div>
                        <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                            <button @click="open = false"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded shadow-sm">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    function tppDetail() {
        return {
            open: false,
            detail: {},
            openDetail(data) {
                this.detail = data;
                this.open = true;
            }
        }
    }
</script>
