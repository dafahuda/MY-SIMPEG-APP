<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Manajemen Setup / User Pegawai</div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data User Pegawai</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Kelola akun login pegawai sesuai batas akses unit kerja.</p>
                <div class="mt-3 inline-flex rounded-full bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                    {{ $scopeLabel ?? 'Scope akun pegawai' }}
                </div>
            </div>

            <a href="/manajemen_setup/view_form_tambah_user_pegawai"
                class="btn bg-indigo-500 hover:bg-indigo-600 text-white w-full sm:w-auto justify-center">
                <svg class="w-4 h-4 fill-current opacity-70 shrink-0" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span class="ml-2">Tambah User Pegawai</span>
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-200">
                <div class="font-semibold">Filter belum valid.</div>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <form action="/manajemen_setup/data_user_pegawai/cariUserPegawai" method="GET" class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <label for="cariUserPegawai" class="block mb-2 text-sm font-medium text-gray-800 dark:text-gray-100">Cari user pegawai</label>
                    <input type="text" name="cariUserPegawai" id="cariUserPegawai"
                        value="{{ old('cariUserPegawai', $search ?? request('cariUserPegawai')) }}"
                        placeholder="Cari username, nama, atau email pegawai"
                        class="form-input w-full @error('cariUserPegawai') border-red-500 @enderror">
                    @error('cariUserPegawai')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white w-full sm:w-auto justify-center">Terapkan</button>
                    <a href="/manajemen_setup/data_user_pegawai" class="btn bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100 w-full sm:w-auto justify-center">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 sm:flex sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Daftar Akun Pegawai</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if (method_exists($user, 'total'))
                            Menampilkan {{ $user->count() }} dari {{ $user->total() }} hasil; total scope {{ $totalPegawaiUsers ?? $user->total() }} akun.
                        @else
                            Menampilkan {{ $user->count() }} akun.
                        @endif
                    </p>
                </div>
                @if (!empty($search))
                    <span class="mt-3 sm:mt-0 inline-flex rounded-full bg-amber-50 dark:bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-700 dark:text-amber-300">1 filter aktif</span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">No</th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">Username</th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">Nama</th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">Email</th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">Unit Kerja</th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center">Hak Akses</th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse ($user as $data)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-gray-500">
                                    {{ method_exists($user, 'firstItem') ? $user->firstItem() + $loop->index : $loop->iteration }}
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">{{ $data->username }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">{{ $data->name }}</td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">{{ $data->email }}</td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">{{ $data->unit_kerja?->nama_unit ?? '-' }}</td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex rounded-full bg-sky-50 dark:bg-sky-500/10 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-300">{{ $data->role }}</span>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="/manajemen_setup/view_form_edit_user_pegawai/{{ $data->id }}"
                                            class="confirm-edit btn-sm bg-indigo-500 hover:bg-indigo-600 text-white"
                                            data-title="Edit User Pegawai"
                                            data-message="Anda akan membuka form edit data user pegawai ini. Lanjutkan?">
                                            Ubah
                                        </a>
                                        <form action="/manajemen_setup/delete_user_pegawai/{{ $data->id }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="confirm-delete btn-sm bg-red-500 hover:bg-red-600 text-white"
                                                data-title="Konfirmasi hapus"
                                                data-message="Apakah Anda yakin untuk menghapus data user pegawai ini? Data yang dihapus tidak akan bisa dikembalikan.">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">Belum ada akun pegawai yang sesuai.</div>
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        @if (!empty($search))
                                            Ubah kata kunci pencarian atau gunakan tombol Reset untuk melihat semua akun dalam scope Anda.
                                        @else
                                            Tambahkan akun pegawai baru untuk mulai mengelola akses pegawai.
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($user, 'links'))
                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $user->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
