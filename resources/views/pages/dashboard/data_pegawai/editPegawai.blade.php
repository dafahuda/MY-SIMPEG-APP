<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah pegawai</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
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
                <input type="hidden" name="gambarLama" value="{{ $pegawai->foto }}">
                <form action="/data_pegawai/ubah_data_pegawai/{{ $pegawai->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="user_id" class="block mb-2.5 text-sm font-medium text-heading">
                            Hubungkan ke Akun User Pegawai <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                            required>
                            <option value="">-- Pilih Akun User Pegawai --</option>
                            @foreach ($userPegawai as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('user_id', $pegawai->user_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Hanya menampilkan akun dengan role pegawai yang belum
                            terhubung ke pegawai lain.</p>
                    </div>
                    <div class="mb-5">
                        <label for="nip" class="block mb-2.5 text-sm font-medium text-heading">NIP <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nip" name="nip"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan NIP" required value="{{ old('nip', $pegawai->nip) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="nama" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Pegawai <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama pegawai" required value="{{ old('nama', $pegawai->nama) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="gelar_depan" class="w-1/4 text-sm font-medium text-heading">Gelar</label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="gelar_depan" name="gelar_depan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan Gelar depan" required
                                value="{{ old('gelar_depan', $pegawai->gelar_depan) }}" />

                            <input type="text" id="gelar" name="gelar"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                placeholder="Masukan Gelar Belakang" required
                                value="{{ old('gelar', $pegawai->gelar) }}" />
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tmpt_lahir" class="w-1/4 text-sm font-medium text-heading">Tempat, Tanggal
                            Lahir <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tmpt_lahir" name="tmpt_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat lahir" required
                                value="{{ old('tmpt_lahir', $pegawai->tmpt_lahir) }}" />

                            <input type="date" id="tgl_lahir" name="tgl_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tgl_lahir', $pegawai->tgl_lahir) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_kelamin" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="rounded-lg">
                            <option value="laki-laki"
                                {{ $pegawai->status_pernikahan == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan"
                                {{ $pegawai->status_pernikahan == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="agama" class="block mb-2.5 text-sm font-medium text-heading">Agama <span
                                class="text-red-500">*</span></label>
                        <select name="agama" id="agama" class="rounded-lg">
                            <option value="Islam" {{ $pegawai->agama == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Protestan" {{ $pegawai->agama == 'Protestan' ? 'selected' : '' }}>Protestan
                            </option>
                            <option value="Katolik" {{ $pegawai->agama == 'Katolik' ? 'selected' : '' }}>Katolik
                            </option>
                            <option value="Hindu" {{ $pegawai->agama == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ $pegawai->agama == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Kong Hu Cu" {{ $pegawai->agama == 'Kong Hu Cu' ? 'selected' : '' }}>Kong Hu
                                Cu</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="golongan_darah" class="block mb-2.5 text-sm font-medium text-heading">Golongan
                            Darah</label>
                        <select name="golongan_darah" id="golongan_darah">
                            <option value="A" {{ $pegawai->golongan_darah == 'A' ? 'selected' : '' }}>A</option>
                            <option value="AB" {{ $pegawai->golongan_darah == 'AB' ? 'selected' : '' }}>AB
                            </option>
                            <option value="B" {{ $pegawai->golongan_darah == 'B' ? 'selected' : '' }}>B</option>
                            <option value="O" {{ $pegawai->golongan_darah == 'O' ? 'selected' : '' }}>O</option>
                            <option value="Tidak Tahu"
                                {{ $pegawai->golongan_darah == 'Tidak Tahu' ? 'selected' : '' }}>Tidak Tahu</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="status_pernikahan" class="block mb-2.5 text-sm font-medium text-heading">Status
                            Pernikahan <span class="text-red-500">*</span></label>
                        <select name="status_pernikahan" id="status_pernikahan" class="rounded-lg">
                            <option value="Nikah" {{ $pegawai->status_pernikahan == 'Nikah' ? 'selected' : '' }}>
                                Nikah
                            </option>
                            <option value="Belum Nikah"
                                {{ $pegawai->status_pernikahan == 'Belum Nikah' ? 'selected' : '' }}>Belum Nikah
                            </option>
                            <option value="Cerai Mati"
                                {{ $pegawai->status_pernikahan == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                            <option value="Cerai Hidup" {{ $pegawai->status_pernikahan == 'Cerai Hidup' }}>Cerai Hidup
                            </option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nik" class="block mb-2.5 text-sm font-medium text-heading">NIK <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nik" name="nik"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan NIK Pegawai" required value="{{ old('nik', $pegawai->nik) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="no_hp" class="block mb-2.5 text-sm font-medium text-heading">Nomor
                            Telepon <span class="text-red-500">*</span></label>
                        <input type="text" id="no_hp" name="no_hp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nomor Telepon" required
                            value="{{ old('no_hp', $pegawai->no_hp) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="email" name="email"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Email" required value="{{ old('email', $pegawai->email) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="email_gov" class="block mb-2.5 text-sm font-medium text-heading">Email
                            Gov <span class="text-red-500">*</span></label>
                        <input type="text" id="email_gov" name="email_gov"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Email gov" required
                            value="{{ old('email_gov', $pegawai->email_gov) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="alamat" class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="alamat" name="alamat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Alamat" required value="{{ old('alamat', $pegawai->alamat) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="no_npwp" class="block mb-2.5 text-sm font-medium text-heading">No.
                            NPWP <span class="text-red-500">*</span></label>
                        <input type="text" id="no_npwp" name="no_npwp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan No. NPWP" required
                            value="{{ old('no_npwp', $pegawai->no_npwp) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="no_bpjs" class="block mb-2.5 text-sm font-medium text-heading">No.
                            BPJS <span class="text-red-500">*</span></label>
                        <input type="text" id="no_bpjs" name="no_bpjs"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan No. BPJS" required
                            value="{{ old('no_bpjs', $pegawai->no_bpjs) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="status_kepegawaian" class="block mb-2.5 text-sm font-medium text-heading">Status
                            Kepegawaian <span class="text-red-500">*</span></label>
                        <select name="status_kepegawaian" id="status_kepegawaian" class="rounded-lg">
                            <option value="PNS" {{ $pegawai->status_kepegawaian == 'PNS' ? 'selected' : '' }}>PNS
                            </option>
                            <option value="PPPK" {{ $pegawai->status_kepegawaian == 'PPPK' ? 'selected' : '' }}>PPPK
                            </option>
                            <option value="TKK" {{ $pegawai->status_kepegawaian == 'TTK' ? 'selected' : '' }}>TKK
                            </option>
                            <option value="HONORER" {{ $pegawai->status_kepegawaian == 'HONORER' ? 'selected' : '' }}>
                                HONORER</option>
                            <option value="CPNS" {{ $pegawai->status_kepegawaian == 'CPNSN' ? 'selected' : '' }}>
                                CPNS</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="karpeg" class="block mb-2.5 text-sm font-medium text-heading">Karpeg <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="karpeg" name="karpeg"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan karpeg" required value="{{ old('karpeg', $pegawai->karpeg) }}" />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk_cpns" class="w-1/4 text-sm font-medium text-heading">No. SK & TMT
                            CPNS <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk_cpns" name="no_sk_cpns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                placeholder="No. SK CPNS" required
                                value="{{ old('no_sk_cpns', $pegawai->no_sk_cpns) }}" />

                            <input type="date" id="tmt_cpns" name="tmt_cpns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tmt_cpns', $pegawai->tmt_cpns) }}" />
                        </div>
                    </div>

                    <div class="flex items-center mb-5">
                        <label for="no_sk_pns" class="w-1/4 text-sm font-medium text-heading">No. SK & TMT
                            PNS <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk_pns" name="no_sk_pns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                placeholder="No. SK PNS" required
                                value="{{ old('no_sk_pns', $pegawai->no_sk_pns) }}" />

                            <input type="date" id="tmt_pns" name="tmt_pns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tmt_pns', $pegawai->tmt_pns) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="gol_awal" class="block mb-2.5 text-sm font-medium text-heading">Gol
                            Awal <span class="text-red-500">*</span></label>
                        <input type="text" id="gol_awal" name="gol_awal"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Gol Awal" required
                            value="{{ old('gol_awal', $pegawai->gol_awal) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="unit_kerja_id" class="block mb-2.5 text-sm font-medium text-heading">OPD /
                            SKPD /
                            Unit
                            Kerja <span class="text-red-500">*</span></label>
                        <select name="unit_kerja_id" id="unit_kerja_id" class="rounded-lg">
                            <option value="">--Pilih Unit Kerja --</option>
                            @foreach ($unitKerja as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('unit_kerja_id', $pegawai->unit_kerja_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        @if ($pegawai->foto)
                            <img src="{{ asset($pegawai->foto) }}" alt="" width="300px" height="200px">
                        @else
                            <img alt="" class="img-preview img-fluid mb-3 col-sm-5">
                        @endif
                        <div class="img-preview">
                            <label for="foto" class="block mb-2.5 text-sm font-medium text-heading">Foto
                                Pegawai <span class="text-red-500">*</span></label>
                            <input type="file" id="foto" name="foto"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan Foto pegawai" />
                        </div>
                    </div>
                    <div class="mt-5">
                        <p class="font-semibold text-slate-400 text-center">Nilai TPP awal sebelum perhitungan</p>
                    </div>
                    <div class="mb-5">
                        <label for="nilai_tpp" class="block mb-2.5 text-sm font-medium text-heading">Nilai
                            Tpp <span class="text-red-500">*</span></label>
                        <input type="number" id="nilai_tpp" name="nilai_tpp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nilai awai TPP" required
                            value="{{ old('nilai_tpp', $pegawai->nilai_tpp) }}" />
                    </div>
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5
                         text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data pegawai ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/data_pegawai/pegawai"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>

<script text="text/javascript">
    function previewImage() {
        const foto = document.querySelector('#foto');
        const imgPreview = document.querySelector('.img-preview');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(foto.files[0]);

        oFReader.onload = function(oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }
</script>
