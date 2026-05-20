<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-start mb-8 gap-4">
            <div class="mb-4 sm:mb-0">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Manajemen Setup / User Admin</div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Data User Admin</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Kelola akun admin unit kerja. Halaman ini hanya dapat diakses superadmin.</p>
            </div>

            <div>
                <a href="/manajemen_setup/view_form_tambah_user_admin"
                    class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path
                            d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                    <span class="hidden xs:block ml-2">Tambah User Admin</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-300">
                <p class="font-semibold">Filter belum valid.</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5 mb-6">
            <form action="/manajemen_setup/data_user_admin/cariUserAdmin" method="GET" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-end">
                <div class="flex-1">
                    <label for="cariUserAdmin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari User Admin</label>
                    <input type="text" name="cariUserAdmin" id="cariUserAdmin"
                        placeholder="Cari username, nama, atau email admin"
                        value="{{ old('cariUserAdmin', $cariUserAdmin ?? request('cariUserAdmin')) }}"
                        class="form-input w-full @error('cariUserAdmin') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                    @error('cariUserAdmin')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn bg-green-500 hover:bg-green-600 text-white w-full sm:w-auto justify-center">
                        <span>Terapkan</span>
                    </button>
                </div>
                <div>
                    <a href="/manajemen_setup/data_user_admin" class="btn bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-100 w-full sm:w-auto justify-center">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 sm:flex sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100">Daftar Admin</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Menampilkan {{ $user->count() }} dari {{ method_exists($user, 'total') ? $user->total() : $user->count() }} akun admin.
                    </p>
                </div>
                @if (($cariUserAdmin ?? request('cariUserAdmin')))
                    <span class="inline-flex mt-3 sm:mt-0 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">1 filter aktif</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-body dark:text-gray-400">
                    <thead class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">No</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Username</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Nama User</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Email</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">Hak Akses</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">OPD / SKPD / Unit Kerja</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        @forelse ($user as $data)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-gray-800 dark:text-gray-100">{{ $loop->iteration + (($user->currentPage() - 1) * $user->perPage()) }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left">{{ $data->username }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left">{{ $data->name }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left">{{ $data->email }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-center"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">{{ $data->role }}</span></div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="text-left">{{ $data->unit_kerja->nama_unit ?? 'Tidak ada nama unit kerja' }}</div></td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="/manajemen_setup/view_form_edit_user_admin/{{ $data->id }}" class="confirm-edit btn-sm bg-indigo-500 hover:bg-indigo-600 text-white" data-title="Ubah User Admin" data-message="Anda akan membuka form edit data user admin ini. Lanjutkan?">Ubah</a>
                                        <form action="/manajemen_setup/delete_user_admin/{{ $data->id }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="confirm-delete btn-sm bg-red-500 hover:bg-red-600 text-white">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">Belum ada data user admin yang sesuai dengan filter.</div>
                                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ubah kata kunci, reset filter, atau tambah akun admin baru.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (method_exists($user, 'links') && $user->hasPages())
                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $user->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
