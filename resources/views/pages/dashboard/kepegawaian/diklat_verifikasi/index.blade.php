<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <span>Kepegawaian</span>
                    <span class="mx-1">/</span>
                    <span>Verifikasi Pengajuan Diklat</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Verifikasi Pengajuan Diklat</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Periksa bukti diklat yang dikirim pegawai untuk rencana yang sudah ditugaskan sebelum menjadi data diklat resmi.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form action="{{ route('diklat_verifikasi.index') }}" method="GET" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-end" data-testid="diklat-verifikasi-filter-form">
                <div class="flex flex-col gap-1">
                    <label for="search" class="text-sm font-medium text-gray-700 dark:text-gray-300">Cari pengajuan</label>
                    <input id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama pegawai, NIP, diklat, atau tahun" class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded block w-full px-3 py-2.5" />
                </div>
                <div class="flex flex-col gap-1">
                    <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status" class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm rounded block w-full px-3 py-2.5">
                        <option value="">Semua status</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white w-full md:w-auto justify-center">Tampilkan</button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                    Menampilkan {{ $pengajuanDiklat->count() }} dari {{ $pengajuanDiklat->total() }} pengajuan
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Filter aktif tetap dipertahankan saat berpindah halaman.</p>
            </div>
            <div class="overflow-x-auto max-w-full">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" data-testid="diklat-verifikasi-table">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Pegawai</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Rencana Diklat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Dikirim</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($pengajuanDiklat as $pengajuan)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">
                                    <div class="font-medium">{{ $pengajuan->pegawai->nama }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pengajuan->pegawai->unit_kerja->nama_unit ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $pengajuan->rencanaDiklat->nama_diklat_rencana }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Tahun {{ $pengajuan->rencanaDiklat->tahun_rencana }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">{{ $pengajuan->statusLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ optional($pengajuan->submitted_at)->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm"><a href="{{ route('diklat_verifikasi.show', $pengajuan->id) }}" class="inline-flex justify-center rounded border border-indigo-200 px-3 py-1.5 text-indigo-600 hover:border-indigo-300 hover:text-indigo-800 dark:border-indigo-500/30 dark:text-indigo-300">Lihat Detail</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada pengajuan diklat untuk diverifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 flex justify-center">
            {{ $pengajuanDiklat->links() }}
        </div>
    </div>
</x-app-layout>
