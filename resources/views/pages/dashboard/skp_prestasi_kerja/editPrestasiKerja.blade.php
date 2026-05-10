<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah prestasi kerja
                </h1>
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
                <form action="/skp_prestasi_kerja/edit_prestasi_kerja/{{ $prestasiKerja->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai</label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $prestasiKerja->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="periode_nilai_dari" class="w-1/4 text-sm font-medium text-heading">Periode
                            Penilaian <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="periode_nilai_dari" name="periode_nilai_dari"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Dari" required
                                value="{{ old('periode_nilai_dari', $prestasiKerja->periode_nilai_dari) }}" />

                            <input type="date" id="periode_nilai_sampai" name="periode_nilai_sampai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('periode_nilai_sampai', $prestasiKerja->periode_nilai_sampai) }}"
                                placeholder="Sampai" />

                            <input type="text" id="tahun_periode" name="tahun_periode"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tahun_periode', $prestasiKerja->tahun_periode) }}"
                                placeholder="Tahun" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="nama_pejabat_nilai" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Pejabat penilai <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_pejabat_nilai" name="nama_pejabat_nilai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama pejabat penilai" required
                            value="{{ old('nama_pejabat_nilai', $prestasiKerja->nama_pejabat_nilai) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="nama_atasan_pejabat_penilai"
                            class="block mb-2.5 text-sm font-medium text-heading">Nama Atasan Pejabat penilai <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nama_atasan_pejabat_penilai" name="nama_atasan_pejabat_penilai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama atasan pejabat penilai" required
                            value="{{ old('nama_atasan_pejabat_penilai', $prestasiKerja->nama_atasan_pejabat_penilai) }}" />
                    </div>
                    <div class="mb-8 border-t pt-5">
                        <h3 class="text-lg font-bold text-heading mb-4 underline">UNSUR YANG DINILAI</h3>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center mb-6">
                            <label for="skp" class="text-sm font-bold text-heading">A. SKP <span
                                    class="text-red-500">*</span></label>
                            <div class="md:col-span-1">
                                <input type="number" id="skp" name="skp"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                    placeholder="Nilai SKP" required value="{{ old('skp', $prestasiKerja->skp) }}" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="font-bold text-sm text-heading mb-4">B. Perilaku Kerja</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-4 ml-4">

                                <div class="flex flex-col gap-2">
                                    <label for="orientasi_pelayanan" class="text-xs font-medium text-body">1. Orientasi
                                        Pelayanan <span class="text-red-500">*</span></label>
                                    <input type="number" id="orientasi_pelayanan" name="orientasi_pelayanan"
                                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                        required
                                        value="{{ old('orientasi_pelayanan', $prestasiKerja->orientasi_pelayanan) }}"
                                        placeholder="0" />
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="integritas" class="text-xs font-medium text-body">2. Integritas <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" id="integritas" name="integritas"
                                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                        required value="{{ old('integritas', $prestasiKerja->integritas) }}"
                                        placeholder="0" />
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="komitmen" class="text-xs font-medium text-body">3. Komitmen <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" id="komitmen" name="komitmen"
                                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                        required value="{{ old('komitmen', $prestasiKerja->komitmen) }}"
                                        placeholder="0" />
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="disiplin" class="text-xs font-medium text-body">4. Disiplin <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" id="disiplin" name="disiplin"
                                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                        required value="{{ old('disiplin', $prestasiKerja->disiplin) }}"
                                        placeholder="0" />
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="kerjasama" class="text-xs font-medium text-body">5. Kerjasama <span
                                            class="text-red-500">*</span></label>
                                    <input type="number" id="kerjasama" name="kerjasama"
                                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                        required value="{{ old('kerjasama', $prestasiKerja->kerjasama) }}"
                                        placeholder="0" />
                                </div>

                                <div class="flex flex-col gap-2">
                                    <label for="kepemimpinan" class="text-xs font-medium text-body">6.
                                        Kepemimpinan <span class="text-red-500">*</span></label>
                                    <input type="number" id="kepemimpinan" name="kepemimpinan"
                                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                        required value="{{ old('kepemimpinan', $prestasiKerja->kepemimpinan) }}"
                                        placeholder="0" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tgl_keberatan_pegawai" class="w-1/4 text-sm font-medium text-heading">Keberatan
                            Pegawai <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="tgl_keberatan_pegawai" name="tgl_keberatan_pegawai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tanggal keberatan pegawai" required
                                value="{{ old('tgl_keberatan_pegawai', $prestasiKerja->tgl_keberatan_pegawai) }}" />

                            <textarea id="isi_keberatan" name="isi_keberatan" rows="4" cols="50" placeholder="Isi keberatan"
                                class="rounded-lg">{{ old('isi_keberatan', $prestasiKerja->isi_keberatan) }}</textarea>
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tgl_pejabat_penilai" class="w-1/4 text-sm font-medium text-heading">Tanggapan
                            pejabat penilai <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="tgl_pejabat_penilai" name="tgl_pejabat_penilai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tanggal tanggapan pejabat penilai" required
                                value="{{ old('tgl_pejabat_penilai', $prestasiKerja->tgl_pejabat_penilai) }}" />

                            <textarea id="isi_tanggapan" name="isi_tanggapan" rows="4" cols="50" placeholder="Isi tanggapan"
                                class="rounded-lg">{{ old('isi_tanggapan', $prestasiKerja->isi_tanggapan) }}</textarea>
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tgl_keputusan_atasan_pejabat_penilai"
                            class="w-1/4 text-sm font-medium text-heading">Tanggapan atasan pejabat penilai <span
                                class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="tgl_keputusan_atasan_pejabat_penilai"
                                name="tgl_keputusan_atasan_pejabat_penilai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tanggal keputusan atasan pejabat penilai" required
                                value="{{ old('tgl_keputusan_atasan_pejabat_penilai', $prestasiKerja->tgl_keputusan_atasan_pejabat_penilai) }}" />

                            <textarea id="isi_keputusan" name="isi_keputusan" rows="4" cols="50" placeholder="Isi keputusan"
                                class="rounded-lg">{{ old('isi_keputusan', $prestasiKerja->isi_keputusan) }}</textarea>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="rekomendasi" class="block mb-2.5 text-sm font-medium text-heading">Rekomendasi
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="rekomendasi" name="rekomendasi"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan rekomendasi" required
                            value="{{ old('rekomendasi', $prestasiKerja->rekomendasi) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="tgl_diterima_pegawai" class="block mb-2.5 text-sm font-medium text-heading">Tgl
                            diterima pegawai <span class="text-red-500">*</span></label>
                        <input type="date" id="tgl_diterima_pegawai" name="tgl_diterima_pegawai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tanggal diterima pegawai" required
                            value="{{ old('tgl_diterima_pegawai', $prestasiKerja->tgl_diterima_pegawai) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="tgl_diterima_atasan" class="block mb-2.5 text-sm font-medium text-heading">Tgl
                            diterima atasan <span class="text-red-500">*</span></label>
                        <input type="date" id="tgl_diterima_atasan" name="tgl_diterima_atasan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tanggal diterima pegawai" required
                            value="{{ old('tgl_diterima_atasan', $prestasiKerja->tgl_diterima_atasan) }}" />
                    </div>
                    <button type="button"
                        class="confirm-save text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none"
                        data-title="Konfirmasi Simpan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data prestasi kerja ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/skp_prestasi_kerja/data_prestasi_kerja"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
