<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header -->
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Riwayat</span>
                    <span class="mx-1">/</span>
                    <a href="/notifikasi_kgb/data_notifikasi_kgb" class="hover:underline">Kenaikan Gaji Berkala</a>
                    <span class="mx-1">/</span>
                    <span>Pegawai : {{ $pegawai->nama }}</span>
                    <span class="mx-1">/</span>
                    <span>NIP : {{ $pegawai->nip }}</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Riwayat</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 p-6">

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Form master kenaikan gaji berkala</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/notifikasi_kgb/buat_kgb" method="POST">
                @csrf
                <input type="hidden" name="pegawai_id" value="{{ $pegawai->id }}">
                <input type="hidden" name="periode" value="{{ date('Y') }}">

                {{-- Nomor dan Tanggal KGB --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Nomor dan Tanggal KGB <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 gap-3">
                        <input type="text" name="no_kgb" value="{{ old('no_kgb') }}" placeholder="Nomor KGB"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                        <div class="relative w-full">
                            <input type="date" name="tgl_kgb" value="{{ old('tgl_kgb') }}"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                        </div>
                    </div>
                </div>

                {{-- Divider: Gaji Pokok Lama --}}
                <div class="mb-4 mt-6">
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 text-center">
                        Gaji Pokok Lama ( Atas Dasar Surat Keputusan Terakhir Tentang Gaji / Pangkat ) Yang Ditetapkan
                        Oleh :
                    </p>
                </div>

                {{-- Pejabat --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Pejabat <span class="text-red-500">*</span>
                    </label>
                    <div class="flex-1">
                        <input type="text" name="pejabat" value="{{ old('pejabat') }}"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                    </div>
                </div>

                {{-- Nomor dan Tanggal SK Terakhir --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Nomor dan Tanggal <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 gap-3">
                        <input type="text" name="no_sk_terakhir" value="{{ old('no_sk_terakhir') }}"
                            placeholder="Nomor SK"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                        <div class="relative w-full">
                            <input type="date" name="tgl_sk_terakhir" value="{{ old('tgl_sk_terakhir') }}"
                                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                        </div>
                    </div>
                </div>

                {{-- Tanggal Berlakunya Gaji --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Tanggal Berlakunya Gaji <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex-1">
                        <input type="date" name="tgl_berlaku_gaji" value="{{ old('tgl_berlaku_gaji') }}"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                    </div>
                </div>

                {{-- Masa Kerja dan Gaji Lama --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Masa Kerja dan Gaji Lama <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 gap-3">
                        <input type="text" name="masa_kerja_lama_tahun" value="{{ old('masa_kerja_lama_tahun') }}"
                            placeholder="Tahun"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                        <input type="text" name="masa_kerja_lama_bulan" value="{{ old('masa_kerja_lama_bulan') }}"
                            placeholder="Bulan"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                    </div>
                </div>

                {{-- Divider: Diberikan Kenaikan Gaji Berkala --}}
                <div class="mb-4 mt-6">
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 text-center">
                        Diberikan Kenaikan Gaji Berkala Hingga Memperoleh :
                    </p>
                </div>

                {{-- Gaji Baru / Terbilang --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Gaji Baru / Terbilang <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 gap-3">
                        <input type="text" name="gaji_baru" value="{{ old('gaji_baru') }}" placeholder="Nominal"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                        <input type="text" name="gaji_baru_terbilang" value="{{ old('gaji_baru_terbilang') }}"
                            placeholder="Terbilang"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                    </div>
                </div>

                {{-- Masa Kerja / Golongan Baru --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Masa Kerja / Golongan <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-1 gap-3">
                        <input type="text" name="masa_kerja_baru_tahun"
                            value="{{ old('masa_kerja_baru_tahun') }}" placeholder="Tahun"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                        <input type="text" name="masa_kerja_baru_bulan"
                            value="{{ old('masa_kerja_baru_bulan') }}" placeholder="Bulan"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                    </div>
                </div>

                {{-- Terhitung Mulai Tanggal --}}
                <div class="flex flex-col sm:flex-row sm:items-center mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0">
                        Terhitung Mulai Tanggal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative flex-1">
                        <input type="date" name="tmt_kgb" value="{{ old('tmt_kgb') }}"
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                    </div>
                </div>

                {{-- Tembusan (dinamis, bisa tambah baris) --}}
                <div x-data="tembusanForm()" class="flex flex-col sm:flex-row sm:items-start mb-5 gap-2">
                    <label
                        class="sm:w-64 text-sm font-medium text-gray-700 dark:text-gray-300 text-right pr-4 shrink-0 pt-2.5">
                        Tembusan <span class="text-red-500">*</span>
                    </label>
                    <div class="flex-1 space-y-2">
                        <template x-for="(item, index) in rows" :key="index">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="'tembusan[' + index + ']'" x-model="rows[index]"
                                    class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5" />
                                <button type="button" @click="removeRow(index)" x-show="rows.length > 1"
                                    class="text-red-500 hover:text-red-700 shrink-0">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                        <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zM0 3h16v2H0V3zm3 0V1h10v2H3z" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="addRow()"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 mt-1">
                            <svg class="w-3 h-3 fill-current" viewBox="0 0 16 16">
                                <path
                                    d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                            </svg>
                            Tambah Tembusan
                        </button>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path
                                d="M13.5 0H2.5A2.5 2.5 0 000 2.5v11A2.5 2.5 0 002.5 16h11a2.5 2.5 0 002.5-2.5v-11A2.5 2.5 0 0013.5 0zM8 12a3 3 0 110-6 3 3 0 010 6zm4-8H4V2h8v2z" />
                        </svg>
                        Save
                    </button>
                    <a href="/notifikasi_kgb/data_notifikasi_kgb"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded shadow-sm">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                            <path d="M3.7 7.3L10 1l1.4 1.4L6.8 7l4.6 4.6L10 13 3.7 7.3z" />
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    function tembusanForm() {
        return {
            rows: ['', '', '', '', ''],
            addRow() {
                this.rows.push('');
            },
            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            }
        }
    }
</script>
