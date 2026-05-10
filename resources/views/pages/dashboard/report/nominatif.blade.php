<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Report</span>
                    <span>/</span>
                    <span>Nominatif</span>
                    @if ($selectedUnitKerja)
                        <span>/</span>
                        <span class="text-blue-500 font-medium">{{ $selectedUnitKerja->nama_unit }}</span>
                    @endif
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Report <span class="text-base font-normal text-gray-500 dark:text-gray-400">Nominatif</span>
                </h1>
            </div>
            @if ($selectedUnitKerja && $pegawaiList->isNotEmpty())
                <div class="flex gap-2">
                    <a href="{{ route('report.nominatif.print', ['unit_kerja_id' => $selectedUnitKerja->id]) }}"
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
            <form method="GET" action="{{ url('/report/nominatif') }}"
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
            {{-- Report Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-6">

                {{-- Kop Laporan --}}
                <div class="text-center mb-6">
                    <p class="font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wide">
                        DAFTAR NOMINATIF PEGAWAI NEGERI SIPIL
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        PER {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('d F Y')) }}
                    </p>
                </div>

                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 uppercase">
                    {{ $selectedUnitKerja->nama_unit }}
                    @if ($instansi)
                        {{ strtoupper($instansi->kabupaten_kota) }} {{ strtoupper($instansi->nama_kota_kabupaten) }}
                    @endif
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse border border-gray-300 dark:border-gray-600">
                        {{-- Header row 1 --}}
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <th rowspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center align-middle w-8">NO</th>
                                <th colspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">NAMA</th>
                                <th rowspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center align-middle">JNS KELAMIN</th>
                                <th colspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">PANGKAT TERAKHIR</th>
                                <th colspan="3" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">JABATAN</th>
                                <th rowspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center align-middle">PEND / JURUSAN / LULUS</th>
                                <th rowspan="2" class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center align-middle">ALAMAT & NO TELP</th>
                            </tr>
                            {{-- Header row 2 --}}
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">NAMA</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">TTL / NIP / AGAMA</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">GOL/RUANG</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">TMT</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">NAMA</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">TMT</th>
                                <th class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs">ESL</th>
                            </tr>
                            {{-- Nomor kolom --}}
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                @foreach (range(1, 10) as $n)
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-1 text-center text-xs font-medium">{{ $n }}</td>
                                @endforeach
                                {{-- kolom 10 sudah di-rowspan, jadi kita butuh 10 td tapi header sudah 11 kolom --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($pegawaiList as $index => $pegawai)
                                @php
                                    $pangkat   = $pegawai->pangkat_terakhir;
                                    $jabatan   = $pegawai->jabatan_aktif;
                                    $pendidikan = $pegawai->pendidikan_terakhir;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 align-top">
                                    {{-- No --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    {{-- Nama --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2">
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">
                                            {{ $pegawai->nama }}{{ $pegawai->gelar ? ', ' . $pegawai->gelar : '' }}
                                        </div>
                                    </td>
                                    {{-- TTL / NIP / Agama --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 whitespace-nowrap">
                                        <div>{{ $pegawai->tmpt_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('Y-m-d') }}</div>
                                        <div>{{ $pegawai->nip }}</div>
                                        <div>{{ $pegawai->nik }}</div>
                                        <div>{{ $pegawai->agama }}</div>
                                    </td>
                                    {{-- Jenis Kelamin --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2 text-center capitalize">
                                        {{ $pegawai->jenis_kelamin === 'laki-laki' ? 'Laki-laki' : 'Perempuan' }}
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
                                    {{-- Pendidikan / Jurusan / Lulus --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2">
                                        @if ($pendidikan)
                                            <div>{{ $pendidikan->jenjang_pendidikan }}</div>
                                            <div>{{ $pendidikan->nama_sekolah_universitas ?? '' }}</div>
                                            <div class="text-gray-500">{{ $pendidikan->jurusan ?? '' }}</div>
                                            <div class="text-gray-500">
                                                {{ isset($pendidikan->tgl_ijazah)
                                                    ? \Carbon\Carbon::parse($pendidikan->tgl_ijazah)->format('Y-m-d')
                                                    : ($pendidikan->thn_selesai ?? '') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    {{-- Alamat & No Telp --}}
                                    <td class="border border-gray-300 dark:border-gray-600 px-2 py-2">
                                        <div>{{ $pegawai->alamat }}</div>
                                        <div class="text-gray-500 mt-1">{{ $pegawai->no_hp }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="border border-gray-300 dark:border-gray-600 px-4 py-8 text-center text-gray-400 dark:text-gray-500">
                                        Tidak ada data pegawai untuk unit kerja ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer info --}}
                <div class="mt-6 text-xs text-gray-500 dark:text-gray-400">
                    Total pegawai: <strong class="text-gray-700 dark:text-gray-200">{{ $pegawaiList->count() }}</strong> orang
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
