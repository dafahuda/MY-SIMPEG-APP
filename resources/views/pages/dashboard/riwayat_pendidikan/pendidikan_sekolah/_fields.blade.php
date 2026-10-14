                    <div class="mb-5">
                        <label for="nama_sekolah_universitas" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Sekolah / Universitas <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_sekolah_universitas" name="nama_sekolah_universitas"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan riwayat jenjang pendidikan" required
                            @isset($riwayatPendidikanSekolah) value="{{ old('nama_sekolah_universitas', $riwayatPendidikanSekolah->nama_sekolah_universitas) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_ijazah" class="w-1/4 text-sm font-medium text-heading">Nomor dan Tanggal
                            Ijazah <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_ijazah" name="no_ijazah"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor Ijazah" required @isset($riwayatPendidikanSekolah) value="{{ old('no_ijazah', $riwayatPendidikanSekolah->no_ijazah) }}" @endisset />

                            <input type="date" id="tgl_ijazah" name="tgl_ijazah"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($riwayatPendidikanSekolah) value="{{ old('tgl_ijazah', $riwayatPendidikanSekolah->tgl_ijazah) }}" @endisset />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="lokasi" class="block mb-2.5 text-sm font-medium text-heading">Lokasi <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="lokasi" name="lokasi"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Lokasi Sekolah / Universitas" required @isset($riwayatPendidikanSekolah) value="{{ old('lokasi', $riwayatPendidikanSekolah->lokasi) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="jurusan" class="block mb-2.5 text-sm font-medium text-heading">Jurusan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jurusan" name="jurusan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Jurusan atau Prodi" required @isset($riwayatPendidikanSekolah) value="{{ old('jurusan', $riwayatPendidikanSekolah->jurusan) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="nama_kepsek_rektor" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Kepsek / Rektor <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_kepsek_rektor" name="nama_kepsek_rektor"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Nama Kepsek / Rektor" required @isset($riwayatPendidikanSekolah) value="{{ old('nama_kepsek_rektor', $riwayatPendidikanSekolah->nama_kepsek_rektor) }}" @endisset />
                    </div>
