<x-app-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Dokumen Digital
        </h2>
        <nav class="flex text-gray-500 dark:text-gray-400 text-sm mt-1" aria-label="breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li><a href="{{ url('/data_pegawai/pegawai') }}" class="hover:text-blue-600">Data Pegawai</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('pegawai.show', $pegawai->id) }}" class="hover:text-blue-600">Detail</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-gray-700 dark:text-gray-300" aria-current="page">Dokumen</li>
            </ol>
        </nav>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 flex flex-col sm:flex-row items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-2xl">
                📁
            </div>
            <div class="text-center sm:text-left">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    @if($pegawai->gelar_depan) {{ $pegawai->gelar_depan }} @endif
                    {{ $pegawai->nama }}@if($pegawai->gelar), {{ $pegawai->gelar }}@endif
                </h3>
                <p class="text-blue-800 dark:text-blue-300 font-mono text-sm">{{ $pegawai->nip }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $dokumen->count() }} dokumen tersimpan</p>
            </div>
            <div class="sm:ml-auto">
                <a href="{{ route('pegawai.show', $pegawai->id) }}"
                   class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-600">
                    ← Kembali ke Detail
                </a>
            </div>
        </div>
    </div>

    <!-- Form upload -->
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h4 class="font-semibold text-gray-800 dark:text-gray-200">Upload Dokumen Baru</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Format: PDF, JPG, PNG — maksimal 5 MB</p>
        </div>
        <form action="{{ route('dokumen-pegawai.store', $pegawai->id) }}" method="POST" enctype="multipart/form-data"
              class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label for="jenis_dokumen" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Dokumen *</label>
                <select id="jenis_dokumen" name="jenis_dokumen" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm @error('jenis_dokumen') border-red-500 @enderror">
                    <option value="">— Pilih Jenis —</option>
                    @foreach (\App\Models\DokumenPegawai::JENIS_DOKUMEN as $value => $label)
                        <option value="{{ $value }}" @if(old('jenis_dokumen') === $value) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis_dokumen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="nama_dokumen" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Dokumen *</label>
                <input type="text" id="nama_dokumen" name="nama_dokumen" value="{{ old('nama_dokumen') }}" required maxlength="150"
                       placeholder="mis. SK CPNS Tahun 2015"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm @error('nama_dokumen') border-red-500 @enderror">
                @error('nama_dokumen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File *</label>
                <input type="file" id="file" name="file" required accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-sm text-blue-800 dark:text-blue-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('file') border-red-500 @enderror">
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" maxlength="500"
                       placeholder="opsional"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm">
            </div>
            <div class="md:col-span-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    ⬆ Upload Dokumen
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar dokumen -->
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
            <tr>
                <th class="px-6 py-3">Jenis</th>
                <th class="px-6 py-3">Nama Dokumen</th>
                <th class="px-6 py-3">File</th>
                <th class="px-6 py-3">Diupload</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($dokumen as $d)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $d->jenis_dokumen === 'ktp' || $d->jenis_dokumen === 'kk' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'
                                : ($d->jenis_dokumen === 'foto' ? 'bg-pink-100 text-pink-800 dark:bg-pink-900/40 dark:text-pink-300'
                                : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300') }}">
                            {{ $d->jenis_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $d->nama_dokumen }}</p>
                        @if ($d->keterangan)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $d->keterangan }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-700 dark:text-gray-300">{{ $d->file_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $d->ukuran_label }} · {{ strtoupper(str_replace('/', ' / ', $d->mime_type)) }}</p>
                    </td>
                    <td class="px-6 py-4 text-blue-800 dark:text-blue-300">
                        {{ $d->created_at->translatedFormat('d M Y, H:i') }}
                        @if ($d->uploader)
                            <p class="text-xs">oleh {{ $d->uploader->name }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('dokumen-pegawai.show', $d->id) }}" target="_blank" rel="noopener"
                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">Lihat</a>
                        <a href="{{ route('dokumen-pegawai.download', $d->id) }}"
                           class="ml-3 text-green-600 hover:text-green-800 dark:text-green-400 font-medium">Unduh</a>
                        @if (in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <button type="button" x-data
                                    @click="if (confirm('Yakin ingin menghapus dokumen \'{{ addslashes($d->nama_dokumen) }}\'? Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('hapus-dok-{{ $d->id }}').submit() }"
                                    class="ml-3 text-red-600 hover:text-red-800 dark:text-red-400 font-medium">Hapus</button>
                            <form id="hapus-dok-{{ $d->id }}" action="{{ route('dokumen-pegawai.destroy', $d->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        📂 Belum ada dokumen diunggah untuk pegawai ini.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
