<x-app-layout>
    @php
        $selectedRencanaDiklatId = old('rencana_diklat_id', $diklat->rencana_diklat_id ?? '');
    @endphp
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit diklat</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 p-5">
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
                <form action="/kepegawaian/diklat/edit_diklat/{{ $diklat->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="fileLama" value="{{ $diklat->file_sertifikat_diklat }}">
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $diklat->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="rencana_diklat_id" class="block mb-2.5 text-sm font-medium text-heading">Rencana
                            Diklat</label>
                        <select name="rencana_diklat_id" id="rencana_diklat_id" data-testid="diklat-rencana-select"
                            class="rounded-lg">
                            <option value="">-- Tanpa Rencana --</option>
                            @if ($currentRencanaDiklat)
                                <option value="{{ $currentRencanaDiklat->id }}"
                                    data-current-link="true"
                                    data-pegawai-id="{{ $currentRencanaDiklat->pegawai_id }}"
                                    data-tahun-rencana="{{ $currentRencanaDiklat->tahun_rencana }}"
                                    {{ (string) $selectedRencanaDiklatId === (string) $currentRencanaDiklat->id ? 'selected' : '' }}>
                                    {{ $currentRencanaDiklat->nama_diklat_rencana }} (Terhubung saat ini)
                                </option>
                            @endif
                            @foreach ($rencanaDiklatOptions as $item)
                                @continue($currentRencanaDiklat && $item->id == $currentRencanaDiklat->id)
                                <option value="{{ $item->id }}"
                                    data-pegawai-id="{{ $item->pegawai_id }}"
                                    data-tahun-rencana="{{ $item->tahun_rencana }}"
                                    {{ (string) $selectedRencanaDiklatId === (string) $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_diklat_rencana }} ({{ $item->tahun_rencana }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_diklat" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Diklat <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_diklat" name="nama_diklat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama diklat" required
                            value="{{ old('nama_diklat', $diklat->nama_diklat) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="jumlah_jam" class="block mb-2.5 text-sm font-medium text-heading">Jumlah Jam <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jumlah_jam" name="jumlah_jam"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jumlah jam diklat" required value="{{ $diklat->jumlah_jam }}" />
                    </div>
                    <div class="mb-5">
                        <label for="penyelenggara" class="block mb-2.5 text-sm font-medium text-heading">Penyelanggara
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="penyelenggara" name="penyelenggara"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan penyelenggara diklat" required
                            value="{{ old('penyelenggara', $diklat->penyelenggara) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="tempat" class="block mb-2.5 text-sm font-medium text-heading">Tempat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tempat" name="tempat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tempat diklat" required value="{{ old('tempat', $diklat->tempat) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="angkatan" class="block mb-2.5 text-sm font-medium text-heading">Angkatan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="angkatan" name="angkatan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan angkatan diklat" required
                            value="{{ old('angkatan', $diklat->angkatan) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="tahun" class="block mb-2.5 text-sm font-medium text-heading">Tahun <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tahun" name="tahun"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tahun diklat" required value="{{ old('tahun', $diklat->tahun) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sttpp" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            sttpp <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sttpp" name="no_sttpp"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor STTPP" required
                                value="{{ old('no_sttpp', $diklat->no_sttpp) }}" />

                            <input type="date" id="tgl_sttpp" name="tgl_sttpp"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal STTPP"
                                value="{{ old('tgl_sttpp', $diklat->tgl_sttpp) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="file_sertifikat_diklat" class="block mb-2.5 text-sm font-medium text-heading">File
                            sertifikat diklat <span class="text-red-500">*</span></label>
                        <input type="file" id="file_sertifikat_diklat" name="file_sertifikat_diklat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file sertifikat diklat" />
                    </div>
                    <button type="submit"
                        class="confirm-save text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none"
                        data-title="Konfirmasi perubahan"
                        data-message="Apakah yakin dengan perubahan data diklat ini?">Simpan</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/diklat"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
    <script>
        (() => {
            const pegawaiSelect = document.getElementById('pegawai_id');
            const tahunInput = document.getElementById('tahun');
            const rencanaSelect = document.getElementById('rencana_diklat_id');

            if (!pegawaiSelect || !tahunInput || !rencanaSelect) {
                return;
            }

            const filterOptions = () => {
                const selectedPegawaiId = pegawaiSelect.value;
                const selectedTahun = Number.parseInt(tahunInput.value, 10);

                Array.from(rencanaSelect.options).forEach((option, index) => {
                    if (index === 0 || option.dataset.currentLink === 'true') {
                        option.hidden = false;
                        return;
                    }

                    const planPegawaiId = option.dataset.pegawaiId;
                    const planTahun = Number.parseInt(option.dataset.tahunRencana, 10);
                    const matchesPegawai = selectedPegawaiId !== '' && planPegawaiId === selectedPegawaiId;
                    const matchesTahun = Number.isInteger(selectedTahun) && (planTahun === selectedTahun || (planTahun + 1) === selectedTahun);

                    option.hidden = !(matchesPegawai && matchesTahun);

                    if (option.hidden && option.selected) {
                        rencanaSelect.value = '';
                    }
                });
            };

            pegawaiSelect.addEventListener('change', filterOptions);
            tahunInput.addEventListener('input', filterOptions);
            filterOptions();
        })();
    </script>
</x-app-layout>
