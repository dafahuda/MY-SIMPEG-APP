<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('diklat_saya.index') }}" class="hover:text-indigo-600">Diklat Saya</a>
                <span class="mx-1">/</span>
                <span>Detail</span>
            </nav>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">{{ $rencana->nama_diklat_rencana }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Unggah bukti selesai diklat untuk diverifikasi admin.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $hasOfficialDiklat = (bool) $rencana->diklat;
            $isCancelled = $rencana->status === 'cancelled';
            $stateTitle = 'Belum diajukan';
            $stateDescription = 'Rencana diklat ini sudah ditugaskan. Unggah bukti setelah diklat selesai agar admin dapat memverifikasi.';
            $stateClass = 'border-indigo-200 bg-indigo-50 text-indigo-800 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-200';

            if ($pengajuan?->isPending()) {
                $stateTitle = 'Menunggu Verifikasi';
                $stateDescription = 'Bukti diklat sudah dikirim dan sedang menunggu pemeriksaan admin. Pengajuan belum dapat diubah sampai admin meminta revisi.';
                $stateClass = 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200';
            } elseif ($pengajuan?->needsRevision()) {
                $stateTitle = 'Perlu Revisi';
                $stateDescription = 'Admin meminta perbaikan bukti. Perbarui file atau data sertifikat sesuai catatan verifikator.';
                $stateClass = 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200';
            } elseif ($hasOfficialDiklat || $pengajuan?->isApproved()) {
                $stateTitle = 'Terverifikasi';
                $stateDescription = 'Pengajuan ini sudah menjadi riwayat diklat resmi. Data resmi dapat dilihat di bagian Riwayat Diklat Resmi.';
                $stateClass = 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200';
            } elseif ($isCancelled) {
                $stateTitle = 'Tidak Tersedia';
                $stateDescription = 'Rencana diklat ini dibatalkan sehingga bukti tidak dapat diunggah.';
                $stateClass = 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300';
            }
        @endphp

        <div class="mb-6 rounded border px-4 py-3 text-sm {{ $stateClass }}" data-testid="diklat-saya-state-message">
            <p class="font-semibold">{{ $stateTitle }}</p>
            <p class="mt-1">{{ $stateDescription }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-gray-500">Tahun</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $rencana->tahun_rencana }}</dd></div>
                <div><dt class="text-gray-500">Target JP</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $rencana->target_jam }}</dd></div>
                <div><dt class="text-gray-500">Status Pengajuan</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan?->statusLabel() ?? 'Belum diajukan' }}</dd></div>
                <div><dt class="text-gray-500">Status Rencana</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ \App\Support\DiklatGlossary::statusLabel($rencana->status) }}</dd></div>
            </dl>
            @if ($pengajuan?->catatan_verifikator)
                <div class="mt-4 rounded bg-yellow-50 border border-yellow-200 px-4 py-3 text-sm text-yellow-800">
                    Catatan verifikator: {{ $pengajuan->catatan_verifikator }}
                </div>
            @endif
        </div>

        @if ($canSubmit || $canRevise)
            <form method="POST" enctype="multipart/form-data" action="{{ $canRevise ? route('diklat_saya.update', $pengajuan->id) : route('diklat_saya.store', $rencana->id) }}" class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4" data-testid="diklat-saya-upload-form">
                @csrf
                @if ($canRevise)
                    @method('PUT')
                @else
                    <input type="hidden" name="rencana_diklat_id" value="{{ $rencana->id }}">
                @endif

                <div>
                    <label for="file_bukti" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">File bukti diklat</label>
                    <input id="file_bukti" name="file_bukti" type="file" required class="block w-full text-sm text-gray-700 dark:text-gray-200" />
                    <p class="mt-1 text-xs text-gray-500">Format: PDF, DOCX, atau TXT. Maksimal 10 MB.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="nomor_sertifikat" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Nomor sertifikat</label>
                        <input id="nomor_sertifikat" name="nomor_sertifikat" value="{{ old('nomor_sertifikat', $pengajuan?->nomor_sertifikat) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" />
                    </div>
                    <div>
                        <label for="tanggal_sertifikat" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal sertifikat</label>
                        <input id="tanggal_sertifikat" type="date" name="tanggal_sertifikat" value="{{ old('tanggal_sertifikat', optional($pengajuan?->tanggal_sertifikat)->format('Y-m-d')) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" />
                    </div>
                </div>
                <div>
                    <label for="jumlah_jam_realisasi" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah JP realisasi</label>
                    <input id="jumlah_jam_realisasi" type="number" min="1" name="jumlah_jam_realisasi" value="{{ old('jumlah_jam_realisasi', $pengajuan?->jumlah_jam_realisasi) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" />
                </div>
                <div>
                    <label for="catatan_pegawai" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Catatan pegawai</label>
                    <textarea id="catatan_pegawai" name="catatan_pegawai" rows="3" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600">{{ old('catatan_pegawai', $pengajuan?->catatan_pegawai) }}</textarea>
                </div>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">{{ $canRevise ? 'Kirim Revisi' : 'Kirim Bukti' }}</button>
            </form>
        @else
            <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">Pengajuan untuk rencana ini tidak dapat diubah saat ini.</div>
        @endif
    </div>
</x-app-layout>
