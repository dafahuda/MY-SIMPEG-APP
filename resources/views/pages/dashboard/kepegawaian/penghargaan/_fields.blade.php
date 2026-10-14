                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">Pilih Pegawai</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}" @isset($penghargaan)
                                    {{ old('pegawai_id', $penghargaan->pegawai_id) == $item->id ? 'selected' : '' }}
                                    @endisset>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_penghargaan" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Penghargaan <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_penghargaan" name="nama_penghargaan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama penghargaan" required @isset($penghargaan) value="{{ old('nama_penghargaan', $penghargaan->nama_penghargaan) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="instansi_pemberi" class="block mb-2.5 text-sm font-medium text-heading">Instansi
                            Pemberi <span class="text-red-500">*</span></label>
                        <input type="text" id="instansi_pemberi" name="instansi_pemberi"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan instansi pemberi" required @isset($penghargaan) value="{{ old('instansi_pemberi', $penghargaan->instansi_pemberi) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="tingkat_kegiatan" class="block mb-2.5 text-sm font-medium text-heading">Tingkat
                            Kegiatan <span class="text-red-500">*</span></label>
                        <select name="tingkat_kegiatan" id="tingkat_kegiatan" class="rounded-lg">
                            <option value="Lokal" @isset($penghargaan)
                                {{ $penghargaan->tingkat_kegiatan == 'Lokal' ? 'selected' : '' }}
                                @endisset>Lokal</option>
                            <option value="Regional" @isset($penghargaan)
                                {{ $penghargaan->tingkat_kegiatan == 'Regional' ? 'selected' : '' }}
                                @endisset>Regional</option>
                            <option value="Nasional" @isset($penghargaan)
                                {{ $penghargaan->tingkat_kegiatan == 'Nasional' ? 'selected' : '' }}
                                @endisset>Nasional</option>
                            <option value="Internasional" @isset($penghargaan)
                                {{ $penghargaan->tingkat_kegiatan == 'Internasional' ? 'selected' : '' }}
                                @endisset>Internasional</option>
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_penghargaan" class="w-1/4 text-sm font-medium text-heading">Tempat dan
                            Tanggal <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_penghargaan" name="tempat_penghargaan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat penghargaan" required @isset($penghargaan) value="{{ old('tempat_penghargaan', $penghargaan->tempat_penghargaan) }}" @endisset />

                            <input type="date" id="tgl_penghargaan" name="tgl_penghargaan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($penghargaan) value="{{ old('tgl_penghargaan', $penghargaan->tgl_penghargaan) }}" @endisset placeholder="Masukan tanggal penghargaan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="tahun" class="block mb-2.5 text-sm font-medium text-heading">Tahun <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tahun" name="tahun"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tahun penghargaan" required @isset($penghargaan) value="{{ old('tahun', $penghargaan->tahun) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="no_sertifikat" class="block mb-2.5 text-sm font-medium text-heading">Nomor
                            Sertifikat <span class="text-red-500">*</span></label>
                        <input type="text" id="no_sertifikat" name="no_sertifikat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nomor sertifikat" required @isset($penghargaan) value="{{ old('no_sertifikat', $penghargaan->no_sertifikat) }}" @endisset />
                    </div>
