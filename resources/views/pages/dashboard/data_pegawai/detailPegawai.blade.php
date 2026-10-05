<x-app-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Detail Pegawai
        </h2>
        <nav class="flex text-gray-500 dark:text-gray-400 text-sm mt-1" aria-label="breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li><a href="{{ url('/data_pegawai/pegawai') }}" class="hover:text-blue-600">Data Pegawai</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-gray-700 dark:text-gray-300" aria-current="page">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <!-- Header: foto + identitas dasar -->
        <div class="p-6 flex flex-col sm:flex-row items-center gap-6 border-b border-gray-200 dark:border-gray-700">
            @if ($pegawai->foto)
                <img src="{{ asset('storage/' . $pegawai->foto) }}" alt="Foto {{ $pegawai->nama }}"
                     class="w-28 h-28 rounded-full object-cover ring-4 ring-gray-100 dark:ring-gray-700">
            @else
                <div class="w-28 h-28 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-4xl text-gray-500 dark:text-gray-400">
                    {{ strtoupper(substr($pegawai->nama, 0, 1)) }}
                </div>
            @endif
            <div class="text-center sm:text-left">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    @if($pegawai->gelar_depan) {{ $pegawai->gelar_depan }} @endif
                    {{ $pegawai->nama }}@if($pegawai->gelar), {{ $pegawai->gelar }}@endif
                </h3>
                <p class="text-gray-600 dark:text-gray-400 font-mono">{{ $pegawai->nip }}</p>
                <span class="inline-flex mt-2 px-3 py-1 rounded-full text-xs font-semibold
                    {{ $pegawai->status_kepegawaian === 'PNS' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                    {{ $pegawai->status_kepegawaian ?? '-' }}
                </span>
                @if($pegawai->unit_kerja)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $pegawai->unit_kerja->nama ?? '-' }}</p>
                @endif
            </div>
            <div class="sm:ml-auto flex gap-2">
                <a href="{{ route('pegawai.biodata.pdf', $pegawai->id) }}"
                   class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">📄 Unduh PDF</a>
                <a href="{{ route('dokumen-pegawai.index', $pegawai->id) }}"
                   class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">📁 Dokumen</a>
                <a href="{{ url('/data_pegawai/view_form_edit_data_pegawai/' . $pegawai->id) }}"
                   class="px-4 py-2 rounded-lg bg-yellow-500 text-white text-sm font-semibold hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300">Ubah</a>
                <a href="{{ url('/data_pegawai/pegawai') }}"
                   class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Data lengkap -->
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 p-6 text-sm">
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-800 dark:text-gray-200 uppercase text-xs tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2">Identitas</h4>
                <div><dt class="text-gray-500 dark:text-gray-400">NIK</dt><dd class="font-medium text-gray-900 dark:text-gray-100 font-mono">{{ $pegawai->nik ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Tempat, Tanggal Lahir</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->tmpt_lahir ?? '-' }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->translatedFormat('d F Y') ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Jenis Kelamin</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->jenis_kelamin ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Agama</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->agama ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Golongan Darah</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->golongan_darah ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Status Pernikahan</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->status_pernikahan ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Alamat</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->alamat ?? '-' }}</dd></div>
            </div>
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-800 dark:text-gray-200 uppercase text-xs tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2">Kepegawaian & Kontak</h4>
                <div><dt class="text-gray-500 dark:text-gray-400">No. SK CPNS / TMT</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->no_sk_cpns ?? '-' }} / {{ $pegawai->tmt_cpns ? \Carbon\Carbon::parse($pegawai->tmt_cpns)->translatedFormat('d-m-Y') : '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">No. SK PNS / TMT</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->no_sk_pns ?? '-' }} / {{ $pegawai->tmt_pns ? \Carbon\Carbon::parse($pegawai->tmt_pns)->translatedFormat('d-m-Y') : '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Karpeg</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->karpeg ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">No. HP</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->no_hp ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Email</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->email ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">Email Gov</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $pegawai->email_gov ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">NPWP</dt><dd class="font-medium text-gray-900 dark:text-gray-100 font-mono">{{ $pegawai->no_npwp ?? '-' }}</dd></div>
                <div><dt class="text-gray-500 dark:text-gray-400">BPJS</dt><dd class="font-medium text-gray-900 dark:text-gray-100 font-mono">{{ $pegawai->no_bpjs ?? '-' }}</dd></div>
            </div>
        </dl>
    </div>
</x-app-layout>
