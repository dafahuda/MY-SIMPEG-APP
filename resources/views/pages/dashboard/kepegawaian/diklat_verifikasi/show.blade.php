<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-8">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('diklat_verifikasi.index') }}" class="hover:text-indigo-600">Verifikasi Pengajuan Diklat</a>
                <span class="mx-1">/</span>
                <span>Detail</span>
            </nav>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Detail Verifikasi Pengajuan</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tinjau bukti diklat pegawai, data rencana, dan keputusan verifikasi sebelum disetujui menjadi data resmi.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 space-y-6">
            <section>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Data Pegawai</h2>
                <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div><dt class="text-gray-500">Pegawai</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan->pegawai->nama }}</dd></div>
                    <div><dt class="text-gray-500">Unit kerja</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan->pegawai->unit_kerja->nama_unit ?? '-' }}</dd></div>
                </dl>
            </section>

            <section>
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3">Rencana dan Bukti Diklat</h2>
                <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                    <div><dt class="text-gray-500">Rencana Diklat</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan->rencanaDiklat->nama_diklat_rencana }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan->statusLabel() }}</dd></div>
                    <div><dt class="text-gray-500">Nomor sertifikat</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan->nomor_sertifikat ?? '-' }}</dd></div>
                    <div><dt class="text-gray-500">Jumlah JP</dt><dd class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan->jumlah_jam_realisasi ?? '-' }}</dd></div>
                </dl>
            </section>

            @if ($pengajuan->file_bukti)
                <a href="{{ $pengajuan->file_bukti }}" class="inline-flex text-sm text-indigo-600 hover:text-indigo-800">Lihat bukti diklat</a>
            @endif

            @if ($canVerify)
                <div class="grid gap-3 pt-4 border-t border-gray-200 dark:border-gray-700" data-testid="diklat-verifikasi-actions">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Keputusan Admin</h2>
                    <form method="POST" action="{{ route('diklat_verifikasi.approve', $pengajuan->id) }}">
                        @csrf
                        <button type="submit" class="btn bg-green-600 hover:bg-green-700 text-white w-full sm:w-auto justify-center">Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('diklat_verifikasi.reject', $pengajuan->id) }}" class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                        @csrf
                        <input name="catatan_verifikator" placeholder="Catatan revisi" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600" />
                        <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white w-full sm:w-auto justify-center">Minta Revisi</button>
                    </form>
                </div>
            @else
                <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">Pengajuan ini tidak menunggu verifikasi.</div>
            @endif
        </div>
    </div>
</x-app-layout>
