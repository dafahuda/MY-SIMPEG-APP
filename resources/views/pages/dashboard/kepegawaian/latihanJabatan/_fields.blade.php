                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">Pilih Pegawai</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    @isset($latihanJabatan) {{ old('pegawai_id', $latihanJabatan->pegawai_id) == $item->id ? 'selected' : '' }} @endisset>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_pelatih" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Pelatih <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_pelatih" name="nama_pelatih"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama pelatih" required
                            @isset($latihanJabatan) value="{{ old('nama_pelatih', $latihanJabatan->nama_pelatih) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_latihan" class="w-1/4 text-sm font-medium text-heading">Tempat dan Waktu
                            Latihan Jabatan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_latihan" name="tempat_latihan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat latihan" required
                                @isset($latihanJabatan) value="{{ old('tempat_latihan', $latihanJabatan->tempat_latihan) }}" @endisset />

                            <input type="date" id="waktu_latihan" name="waktu_latihan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($latihanJabatan) value="{{ old('waktu_latihan', $latihanJabatan->waktu_latihan) }}" @endisset
                                placeholder="Masukan tanggal latihan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="tahun_latihan" class="block mb-2.5 text-sm font-medium text-heading">Tahun
                            Latihan <span class="text-red-500">*</span></label>
                        <input type="text" id="tahun_latihan" name="tahun_latihan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tahun latihan" required
                            @isset($latihanJabatan) value="{{ old('tahun_latihan', $latihanJabatan->tahun_latihan) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="jumlah_jam" class="block mb-2.5 text-sm font-medium text-heading">Jumlah Jam <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jumlah_jam" name="jumlah_jam"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jumlah jam" required
                            @isset($latihanJabatan) value="{{ old('jumlah_jam', $latihanJabatan->jumlah_jam) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="nomor_sertifikat" class="w-1/4 text-sm font-medium text-heading">Nomor dan Tgl
                            Sertifikat <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="nomor_sertifikat" name="nomor_sertifikat"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor sertifikat" required
                                @isset($latihanJabatan) value="{{ old('nomor_sertifikat', $latihanJabatan->nomor_sertifikat) }}" @endisset />

                            <input type="date" id="tgl_sertifikat" name="tgl_sertifikat"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($latihanJabatan) value="{{ old('tgl_sertifikat', $latihanJabatan->tgl_sertifikat) }}" @endisset
                                placeholder="Masukan tanggal sertifikat" />
                        </div>
                    </div>
