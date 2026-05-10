<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Edit Profile Pegawai</h1>
            </div>
            <!-- Right: Actions -->
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <a href="/profile_saya" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                        <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" transform="rotate(180 8 8)" />
                    </svg>
                    <span class="ml-2">Kembali</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <ul class="list-disc list-inside text-red-800 dark:text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                <form action="{{ route('profile.pegawai.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Upload Foto Section -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Foto Profile</h3>
                        <div class="flex items-center gap-6">
                            <div class="w-32 h-36 rounded overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700">
                                @if ($pegawai->foto)
                                    <img id="preview-foto" src="{{ asset($pegawai->foto) }}" alt="Foto" class="w-full h-full object-cover">
                                @else
                                    <div id="preview-foto" class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Upload Foto Baru
                                </label>
                                <input type="file" name="foto" id="foto" accept="image/*"
                                    class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100
                                    dark:file:bg-indigo-900/30 dark:file:text-indigo-300"
                                    onchange="previewImage(event)">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG, JPEG, PNG, GIF. Max: 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NIP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                NIP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" value="{{ old('nama', $pegawai->nama) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Unit Kerja -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unit Kerja <span class="text-red-500">*</span>
                            </label>
                            <select name="unit_kerja_id" required
                                class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">-- Pilih Unit Kerja --</option>
                                @foreach ($unitKerja as $uk)
                                    <option value="{{ $uk->id }}" {{ old('unit_kerja_id', $pegawai->unit_kerja_id) == $uk->id ? 'selected' : '' }}>
                                        {{ $uk->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Gelar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Gelar
                            </label>
                            <input type="text" name="gelar" value="{{ old('gelar', $pegawai->gelar) }}"
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Tempat Lahir -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tmpt_lahir" value="{{ old('tmpt_lahir', $pegawai->tmpt_lahir) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $pegawai->tgl_lahir) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_kelamin" required
                                class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">-- Pilih --</option>
                                <option value="laki-laki" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <!-- Agama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Agama <span class="text-red-500">*</span>
                            </label>
                            <select name="agama" required
                                class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">-- Pilih --</option>
                                @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" {{ old('agama', $pegawai->agama) == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Golongan Darah -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Golongan Darah <span class="text-red-500">*</span>
                            </label>
                            <select name="golongan_darah" required
                                class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">-- Pilih --</option>
                                @foreach (['A', 'B', 'AB', 'O'] as $gol)
                                    <option value="{{ $gol }}" {{ old('golongan_darah', $pegawai->golongan_darah) == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Pernikahan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status Pernikahan <span class="text-red-500">*</span>
                            </label>
                            <select name="status_pernikahan" required
                                class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">-- Pilih --</option>
                                @foreach (['Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati'] as $status)
                                    <option value="{{ $status }}" {{ old('status_pernikahan', $pegawai->status_pernikahan) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- NIK -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                NIK <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nik" value="{{ old('nik', $pegawai->nik) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- No HP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                No. HP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $pegawai->email) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Email Gov -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Pemerintah <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email_gov" value="{{ old('email_gov', $pegawai->email_gov) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- No NPWP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                No. NPWP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_npwp" value="{{ old('no_npwp', $pegawai->no_npwp) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- No BPJS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                No. BPJS <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_bpjs" value="{{ old('no_bpjs', $pegawai->no_bpjs) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Status Kepegawaian -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status Kepegawaian <span class="text-red-500">*</span>
                            </label>
                            <select name="status_kepegawaian" required
                                class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">-- Pilih --</option>
                                @foreach (['PNS', 'PPPK', 'Honorer'] as $status)
                                    <option value="{{ $status }}" {{ old('status_kepegawaian', $pegawai->status_kepegawaian) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Karpeg -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Karpeg <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="karpeg" value="{{ old('karpeg', $pegawai->karpeg) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- No SK CPNS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                No. SK CPNS
                            </label>
                            <input type="text" name="no_sk_cpns" value="{{ old('no_sk_cpns', $pegawai->no_sk_cpns) }}"
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- TMT CPNS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                TMT CPNS <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', $pegawai->tmt_cpns) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- No SK PNS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                No. SK PNS
                            </label>
                            <input type="text" name="no_sk_pns" value="{{ old('no_sk_pns', $pegawai->no_sk_pns) }}"
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- TMT PNS -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                TMT PNS <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tmt_pns" value="{{ old('tmt_pns', $pegawai->tmt_pns) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Golongan Awal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Golongan Awal <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="gol_awal" value="{{ old('gol_awal', $pegawai->gol_awal) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Nilai TPP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nilai TPP <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="nilai_tpp" value="{{ old('nilai_tpp', $pegawai->nilai_tpp) }}" required
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        </div>

                        <!-- Alamat (full width) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat" rows="3" required
                                class="form-textarea w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('alamat', $pegawai->alamat) }}</textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end gap-3">
                        <a href="/profile_saya" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                            Batal
                        </a>
                        <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                            <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                                <path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z" />
                            </svg>
                            <span class="ml-2">Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('preview-foto');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
