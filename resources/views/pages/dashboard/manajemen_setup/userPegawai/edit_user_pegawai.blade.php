<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit data user pegawai
                </h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <form action="/manajemen_setup/edit_data_user_pegawai/{{ $user->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-5">
                        <label for="username" class="block mb-2.5 text-sm font-medium text-heading">Username <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="username" name="username"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan username" required value="{{ old('username', $user->username) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Nama User <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nama User" required value="{{ old('name', $user->name) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="email" name="email"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Email User" required value="{{ old('email', $user->email) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="unit_kerja_id" class="block mb-2.5 text-sm font-medium text-heading">OPD / SKPD /
                            Unit Kerja <span class="text-red-500">*</span></label>
                        <select name="unit_kerja_id" id="unit_kerja_id" class="rounded-lg">
                            <option value="">--Pilih Unit Kerja --</option>
                            @foreach ($unitKerja as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('unit_kerja_id', $user->unit_kerja_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data user pegawai ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/manajemen_setup/data_user_pegawai"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>

    </div>
</x-app-layout>
