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
                <form action="/data_pegawai/tambah_data_pegawai" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-5">
                        <label for="user_id" class="block mb-2.5 text-sm font-medium text-heading">
                            Hubungkan ke Akun User Pegawai <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                            required>
                            <option value="">-- Pilih Akun User Pegawai --</option>
                            @foreach ($userPegawai as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Hanya menampilkan akun dengan role pegawai yang belum
                            terhubung.</p>
                    </div>
                    <div class="mb-5">
                        <label for="nip" class="block mb-2.5 text-sm font-medium text-heading">NIP <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nip" name="nip"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan NIP" required />
                    </div>
                    <div class="mb-5">
                        <label for="nama" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Pegawai <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama pegawai" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="gelar_depan" class="w-1/4 text-sm font-medium text-heading">Gelar</label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="gelar_depan" name="gelar_depan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan Gelar depan" />

                            <input type="text" id="gelar" name="gelar"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                placeholder="Masukan Gelar Belakang" />
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tmpt_lahir" class="w-1/4 text-sm font-medium text-heading">Tempat, Tanggal
                            Lahir <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tmpt_lahir" name="tmpt_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat lahir" required />

                            <input type="date" id="tgl_lahir" name="tgl_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_kelamin" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            kelamin <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin">
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="agama" class="block mb-2.5 text-sm font-medium text-heading">Agama <span
                                class="text-red-500">*</span></label>
                        <select name="agama" id="agama" class="rounded-lg">
                            <option value="Islam">Islam</option>
                            <option value="Protestan">Protestan</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Kong Hu Cu">Kong Hu Cu</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="golongan_darah" class="block mb-2.5 text-sm font-medium text-heading">Golongan
                            Darah <span class="text-red-500">*</span></label>
                        <select name="golongan_darah" id="golongan_darah" class="rounded-lg">
                            <option value="A">A</option>
                            <option value="AB">AB</option>
                            <option value="B">B</option>
                            <option value="O">O</option>
                            <option value="Tidak Tahu">Tidak Tahu</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="status_pernikahan" class="block mb-2.5 text-sm font-medium text-heading">Status
                            Pernikahan <span class="text-red-500">*</span></label>
                        <select name="status_pernikahan" id="status_pernikahan" class="rounded-lg">
                            <option value="Nikah">Nikah</option>
                            <option value="Belum Nikah">Belum Nikah</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nik" class="block mb-2.5 text-sm font-medium text-heading">NIK <span
                                class="text-red-500">*</span> </label>
                        <input type="text" id="nik" name="nik"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan NIK Pegawai" required />
                    </div>
                    <div class="mb-5">
                        <label for="no_hp" class="block mb-2.5 text-sm font-medium text-heading">Nomor
                            Telepon <span class="text-red-500">*</span></label>
                        <input type="text" id="no_hp" name="no_hp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nomor Telepon" required />
                    </div>
                    <div class="mb-5">
                        <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Email <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="email" name="email"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Email" required />
                    </div>
                    <div class="mb-5">
                        <label for="email_gov" class="block mb-2.5 text-sm font-medium text-heading">Email
                            Gov</label>
                        <input type="text" id="email_gov" name="email_gov"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Email gov" required />
                    </div>
                    <div class="mb-5">
                        <label for="alamat" class="block mb-2.5 text-sm font-medium text-heading">Alamat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="alamat" name="alamat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Alamat" required />
                    </div>
                    <div class="mb-5">
                        <label for="no_npwp" class="block mb-2.5 text-sm font-medium text-heading">No.
                            NPWP <span class="text-red-500">*</span></label>
                        <input type="text" id="no_npwp" name="no_npwp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan No. NPWP" required />
                    </div>
                    <div class="mb-5">
                        <label for="no_bpjs" class="block mb-2.5 text-sm font-medium text-heading">No.
                            BPJS <span class="text-red-500">*</span></label>
                        <input type="text" id="no_bpjs" name="no_bpjs"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan No. BPJS" required />
                    </div>
                    <div class="mb-5">
                        <label for="status_kepegawaian" class="block mb-2.5 text-sm font-medium text-heading">Status
                            Kepegawaian <span class="text-red-500">*</span></label>
                        <select name="status_kepegawaian" id="status_kepegawaian" class="rounded-lg">
                            <option value="PNS">PNS</option>
                            <option value="PPPK">PPPK</option>
                            <option value="TKK">TKK</option>
                            <option value="HONORER">HONORER</option>
                            <option value="CPNS">CPNS</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="karpeg" class="block mb-2.5 text-sm font-medium text-heading">Karpeg <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="karpeg" name="karpeg"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan karpeg" required />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk_cpns" class="w-1/4 text-sm font-medium text-heading">No. SK & TMT
                            CPNS <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk_cpns" name="no_sk_cpns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                placeholder="No. SK CPNS" required />

                            <input type="date" id="tmt_cpns" name="tmt_cpns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required />
                        </div>
                    </div>

                    <div class="flex items-center mb-5">
                        <label for="no_sk_pns" class="w-1/4 text-sm font-medium text-heading">No. SK & TMT
                            PNS <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk_pns" name="no_sk_pns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                placeholder="No. SK PNS" required />

                            <input type="date" id="tmt_pns" name="tmt_pns"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="gol_awal" class="block mb-2.5 text-sm font-medium text-heading">Gol
                            Awal <span class="text-red-500">*</span> </label>
                        <input type="text" id="gol_awal" name="gol_awal"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Gol Awal" required />
                    </div>
                    <div class="mb-5">
                        <label class="block mb-2.5 text-sm font-medium text-heading">
                            OPD / SKPD / Unit Kerja <span class="text-red-500">*</span>
                        </label>

                        @if (auth()->user()->role === 'admin')
                            <!-- Admin: dikunci -->
                            <input type="hidden" name="unit_kerja_id" value="{{ auth()->user()->unit_kerja_id }}">

                            <select disabled class="bg-gray-100 cursor-not-allowed rounded-lg">
                                @foreach ($unitKerja as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $item->id == auth()->user()->unit_kerja_id ? 'selected' : '' }}>
                                        {{ $item->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <!-- Superadmin: bebas pilih -->
                            <select name="unit_kerja_id" id="unit_kerja_id" class="rounded-lg">
                                <option value="">--Pilih Unit Kerja --</option>
                                @foreach ($unitKerja as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_unit }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="mb-5">
                        <label for="foto" class="block mb-2.5 text-sm font-medium text-heading">Foto
                            Pegawai <span class="text-red-500">*</span></label>
                        <input type="file" id="foto" name="foto"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Foto pegawai" required />
                    </div>
                    <div class="mt-5">
                        <p class="font-semibold text-slate-400 text-center">Nilai TPP awal sebelum perhitungan</p>
                    </div>
                    <div class="mb-5">
                        <label for="nilai_tpp" class="block mb-2.5 text-sm font-medium text-heading">Nilai
                            Tpp <span class="text-red-500">*</span></label>
                        <input type="number" id="nilai_tpp" name="nilai_tpp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nilai awai TPP" required />
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/data_pegawai/pegawai"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
