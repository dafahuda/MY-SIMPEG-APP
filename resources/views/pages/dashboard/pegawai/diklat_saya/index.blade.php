<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Pegawai</span>
                    <span class="mx-1">/</span>
                    <span>Diklat Saya</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Diklat Saya</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Rencana diklat ditugaskan oleh admin/instansi. Pegawai hanya mengunggah bukti untuk diklat yang sudah ditugaskan, lalu memantau verifikasi dan riwayat resminya di sini.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-900/20 dark:text-green-300">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6" data-testid="diklat-saya-summary-cards">
            <div class="rounded-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rencana Ditugaskan</p>
                <p class="mt-2 text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $summary['assigned_count'] }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Semua rencana diklat dari admin/instansi.</p>
            </div>
            <div class="rounded-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu Verifikasi</p>
                <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $summary['pending_count'] }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Bukti sudah dikirim dan menunggu admin.</p>
            </div>
            <div class="rounded-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Perlu Revisi</p>
                <p class="mt-2 text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $summary['revision_count'] }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pengajuan yang perlu diperbaiki pegawai.</p>
            </div>
            <div class="rounded-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Riwayat Resmi</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $summary['official_count'] }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Diklat yang sudah masuk data resmi.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6" data-testid="diklat-saya-perlu-tindakan">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Perlu Tindakan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Diklat yang perlu bukti awal atau revisi dari pegawai.</p>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($actionItems as $rencana)
                    @php
                        $pengajuan = $rencana->pengajuanDiklat->first();
                        $needsRevision = $pengajuan?->needsRevision() === true;
                    @endphp
                    <div class="p-5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ $rencana->nama_diklat_rencana }}</h3>
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $needsRevision ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300' }}">
                                    {{ $needsRevision ? 'Perlu Revisi' : 'Belum diajukan' }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tahun {{ $rencana->tahun_rencana }} · Target {{ $rencana->target_jam ?? 0 }} JP</p>
                            @if ($needsRevision && $pengajuan->catatan_verifikator)
                                <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">Catatan admin: {{ $pengajuan->catatan_verifikator }}</p>
                            @endif
                        </div>
                        <a href="{{ route('diklat_saya.show', $rencana->id) }}" class="inline-flex shrink-0 items-center justify-center rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            {{ $needsRevision ? 'Kirim Revisi' : 'Upload Bukti' }}
                        </a>
                    </div>
                @empty
                    <div class="p-5 text-sm text-gray-500 dark:text-gray-400">Tidak ada tindakan yang perlu dilakukan saat ini.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Rencana Diklat Ditugaskan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" data-testid="diklat-saya-rencana-table">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Nama Diklat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tahun</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status Pengajuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($rencanaDiklat as $rencana)
                            @php
                                $pengajuan = $rencana->pengajuanDiklat->first();
                                $hasOfficialDiklat = (bool) $rencana->diklat;
                                $isCancelled = $rencana->status === 'cancelled';
                                $actionLabel = match (true) {
                                    $isCancelled => 'Tidak Tersedia',
                                    $hasOfficialDiklat => 'Terverifikasi',
                                    $pengajuan?->isPending() === true => 'Lihat Status',
                                    $pengajuan?->needsRevision() === true => 'Kirim Revisi',
                                    default => 'Upload Bukti',
                                };
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $rencana->nama_diklat_rencana }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $rencana->tahun_rencana }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $pengajuan?->statusLabel() ?? 'Belum diajukan' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('diklat_saya.show', $rencana->id) }}" class="text-indigo-600 hover:text-indigo-800">{{ $actionLabel }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada rencana diklat yang ditugaskan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100">Riwayat Diklat Resmi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" data-testid="diklat-saya-riwayat-table">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Nama Diklat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tahun</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">No. Sertifikat / STTPP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Jumlah JP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($riwayatDiklat as $diklat)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">{{ $diklat->nama_diklat }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $diklat->tahun }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $diklat->no_sttpp ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $diklat->jumlah_jam }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada riwayat diklat resmi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
