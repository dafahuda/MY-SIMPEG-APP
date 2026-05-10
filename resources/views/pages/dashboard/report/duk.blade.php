<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Report</span>
                    <span>/</span>
                    <span>DUK</span>
                    @if ($selectedUnitKerja)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedUnitKerja->nama_unit }}</span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Report <span class="text-base font-normal text-gray-500 dark:text-gray-400">DUK</span>
                </h1>
            </div>
            @if ($selectedUnitKerja && $pegawaiList->isNotEmpty())
                <div class="flex gap-2">
                    <a href="{{ route('report.duk.print', ['unit_kerja_id' => $selectedUnitKerja->id]) }}"
                        target="_blank"
                        class="btn bg-green-500 hover:bg-green-600 text-white">
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                            <path d="M15 10h-2V2H3v8H1a1 1 0 00-1 1v3a1 1 0 001 1h14a1 1 0 001-1v-3a1 1 0 00-1-1zM4 3h8v7H4V3zm8 10H4v-2h8v2zm2 0h-1v-2H3v2H2v-2h1V9h10v2h1v2z"/>
                        </svg>
                        <span class="ml-2">Print</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- Filter Card --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form method="GET" action="{{ url('/report/duk') }}"
                class="flex flex-col sm:flex-row items-end gap-3">
                <div class="flex flex-col gap-1 flex-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Pilih OPD / SKPD / Unit Kerja
                    </label>
                    <select name="unit_kerja_id"
                        class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded focus:ring-indigo-500 focus:border-indigo-500 block w-full px-3 py-2.5">
                        <option value="">-- Pilih Unit Kerja --</option>
                        @foreach ($unitKerjaList as $uk)
                            <option value="{{ $uk->id }}"
                                {{ request('unit_kerja_id') == $uk->id ? 'selected' : '' }}>
                                {{ $uk->nama_unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path d="M15.7 14.3l-3.7-3.7c.9-1.2 1.4-2.6 1.4-4.1C13.4 2.9 10.5 0 7 0S.6 2.9.6 6.5 3.5 13 7 13c1.5 0 2.9-.5 4.1-1.4l3.7 3.7.9-.9zM2 6.5C2 3.7 4.2 1.5 7 1.5S12 3.7 12 6.5 9.8 11.5 7 11.5 2 9.3 2 6.5z"/>
                    </svg>
                    Get Report
                </button>
            </form>
        </div>

        @if ($selectedUnitKerja)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-6">

                {{-- Kop --}}
                <div class="text-center mb-4">
                    <p class="font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wide text-sm">
                        DAFTAR URUT KEPANGKATAN
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 uppercase">
                        APARATUR SIPIL NEGARA (ASN) PADA {{ strtoupper($selectedUnitKerja->nama_unit) }}
                        @if ($instansi)
                            {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
                        @endif
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">TAHUN {{ $tahun }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse border border-gray-300 dark:border-gray-600">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <th rowspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center align-middle w-8">NO</th>
                                <th colspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">NAMA</th>
                                <th colspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">PANGKAT TERAKHIR</th>
                                <th colspan="3" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">JABATAN</th>
                                <th colspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">MK GOL</th>
                                <th colspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">PEND AKHIR</th>
                            </tr>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">NAMA</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">NIP</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">GOL/RUANG</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">TMT</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">NAMA</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">TMT</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">ESL</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">THN</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">BLN</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">ASAL / TK</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">TLS</th>
                            </tr>
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-400 dark:text-gray-500">
                                @foreach (range(1, 12) as $n)
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs font-medium">{{ $n }}</td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($pegawaiList as $index => $pegawai)
                                @php
                                    $pangkat    = $pegawai->pangkat_terakhir;
                                    $jabatan    = $pegawai->jabatan_aktif;
                                    $pendidikan = $pegawai->pendidikan_terakhir;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 align-top">
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">{{ $index + 1 }}</td>
                                    {{-- Nama --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}
                                        </div>
                                        <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">
                                            {{ $pegawai->tmpt_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') }}
                                        </div>
                                    </td>
                                    {{-- NIP --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 whitespace-nowrap">
                                        {{ $pegawai->nip }}
                                    </td>
                                    {{-- Gol/Ruang --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">
                                        @if ($pangkat)
                                            <div>{{ $pangkat->master_pangkat->nama_pangkat ?? '-' }}</div>
                                            <div class="text-gray-500">({{ $pangkat->master_golongan->nama_golongan ?? '-' }})</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    {{-- TMT Pangkat --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center whitespace-nowrap">
                                        {{ $pangkat && $pangkat->tmt_pangkat_mulai ? \Carbon\Carbon::parse($pangkat->tmt_pangkat_mulai)->format('Y-m-d') : '-' }}
                                    </td>
                                    {{-- Nama Jabatan --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2">
                                        {{ $jabatan->master_jabatan->nama_jabatan ?? '-' }}
                                    </td>
                                    {{-- TMT Jabatan --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center whitespace-nowrap">
                                        {{ $jabatan && $jabatan->tmt_jabatan_mulai ? \Carbon\Carbon::parse($jabatan->tmt_jabatan_mulai)->format('Y-m-d') : '-' }}
                                    </td>
                                    {{-- Eselon --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">
                                        {{ $jabatan->master_eselon->nama_eselon ?? '-' }}
                                    </td>
                                    {{-- MK Gol Tahun --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">
                                        {{ $pegawai->mk_thn }}
                                    </td>
                                    {{-- MK Gol Bulan --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">
                                        {{ $pegawai->mk_bln }}
                                    </td>
                                    {{-- Pend Akhir Asal/TK --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2">
                                        @if ($pendidikan)
                                            <div>{{ $pendidikan->nama_sekolah_universitas ?? '-' }}</div>
                                            <div class="text-gray-500">{{ $pendidikan->jenjang_pendidikan }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    {{-- Pend Akhir Tls (Tahun Lulus) --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center whitespace-nowrap">
                                        @if ($pendidikan)
                                            {{ isset($pendidikan->tgl_ijazah)
                                                ? \Carbon\Carbon::parse($pendidikan->tgl_ijazah)->format('Y-m-d')
                                                : ($pendidikan->thn_selesai ?? '-') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="border border-gray-300 dark:border-gray-600 px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                        Tidak ada data pegawai untuk unit kerja ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    Total pegawai: <strong class="text-gray-700 dark:text-gray-200">{{ $pegawaiList->count() }}</strong> orang
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
