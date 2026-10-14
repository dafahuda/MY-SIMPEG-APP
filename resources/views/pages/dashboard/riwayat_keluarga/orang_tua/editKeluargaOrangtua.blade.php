<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form ubah data riwayat
                    keluarga Orang Tua Pegawai</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 mb-5">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="/riwayat_keluarga/orang_tua/edit_data_orang_tua/{{ $riwayatKeluargaOrangTua->id }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai</label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">Pilih Pegawai</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $riwayatKeluargaOrangTua->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @include('pages.dashboard.riwayat_keluarga.orang_tua._fields', ['fieldValues' => [
                        'nik' => old('nik', $riwayatKeluargaOrangTua->nik),
                        'nama' => $riwayatKeluargaOrangTua->nama,
                        'tempat_lahir' => old('tempat_lahir', $riwayatKeluargaOrangTua->tempat_lahir),
                        'tgl_lahir' => old('tgl_lahir', $riwayatKeluargaOrangTua->tgl_lahir),
                    ]])
                    <div class="mb-5">
                        <label for="jenis_kelamin" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="rounded-lg">
                            <option value="laki-laki"
                                {{ $riwayatKeluargaOrangTua->jenis_kelamin == 'laki-laki' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="perempuan"
                                {{ $riwayatKeluargaOrangTua->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>
                                Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="pendidikan" class="block mb-2.5 text-sm font-medium text-heading">Pendidikan <span
                                class="text-red-500">*</span></label>
                        <select name="pendidikan" id="pendidikan" class="rounded-lg">
                            <option value="SD"
                                {{ $riwayatKeluargaOrangTua->pendidikan == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SLTP"
                                {{ $riwayatKeluargaOrangTua->pendidikan == 'SLTP' ? 'selected' : '' }}>SLTP</option>
                            <option value="SLTA"
                                {{ $riwayatKeluargaOrangTua->pendidikan == 'SLTA' ? 'selected' : '' }}>SLTA</option>
                            <option value="D3"
                                {{ $riwayatKeluargaOrangTua->pendidikan == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1"
                                {{ $riwayatKeluargaOrangTua->pendidikan == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2"
                                {{ $riwayatKeluargaOrangTua->pendidikan == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3"
                                {{ $riwayatKeluargaOrangTua->pendidikan == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="pekerjaan" class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan" name="pekerjaan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Pekerjaan Orang Tua" required
                            value="{{ old('pekerjaan', $riwayatKeluargaOrangTua->pekerjaan) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="status_hubungan" class="block mb-2.5 text-sm font-medium text-heading">Status
                            Hubungan <span class="text-red-500">*</span></label>
                        <select name="status_hubungan" id="status_hubungan" class="rounded-lg">
                            <option value="Ayah Kandung"
                                {{ $riwayatKeluargaOrangTua->status_hubungan == 'Ayah Kandung' ? 'selected' : '' }}>
                                Ayah Kandung</option>
                            <option value="Ibu Kandung"
                                {{ $riwayatKeluargaOrangTua->status_hubungan == 'Ibu Kandung' ? 'selected' : '' }}>Ibu
                                Kandung</option>
                        </select>
                    </div>
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-blue-500 shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data orang tua ini?">Simpan</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/riwayat_keluarga/orang_tua"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>

    </div>
</x-app-layout>
