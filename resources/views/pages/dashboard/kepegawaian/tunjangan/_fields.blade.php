                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">Pilih Pegawai</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}" @isset($tunjangan)
                                    {{ old('pegawai_id', $tunjangan->pegawai_id) == $item->id ? 'selected' : '' }}
                                    @endisset>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_tunjangan" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            tunjangan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_tunjangan" name="no_tunjangan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor tunjangan" required
                                @isset($tunjangan) value="{{ old('no_tunjangan', $tunjangan->no_tunjangan) }}" @endisset />

                            <input type="date" id="tgl_tunjangan" name="tgl_tunjangan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($tunjangan) value="{{ old('tgl_tunjangan', $tunjangan->tgl_tunjangan) }}" @endisset
                                placeholder="Masukan tanggal tunjangan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_tunjangan_anak" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Tunjangan <span class="text-red-500">*</span></label>
                        <input type="text" id="jenis_tunjangan_anak" name="jenis_tunjangan_anak"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tunjangan anak" required
                            @isset($tunjangan) value="{{ old('jenis_tunjangan_anak', $tunjangan->jenis_tunjangan_anak) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="terhitung_mulai" class="block mb-2.5 text-sm font-medium text-heading">Terhitung
                            mulai <span class="text-red-500">*</span></label>
                        <input type="date" id="terhitung_mulai" name="terhitung_mulai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Terhitung dari" required
                            @isset($tunjangan) value="{{ old('terhitung_mulai', $tunjangan->terhitung_mulai) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="akta_perkawinan_dari" class="block mb-2.5 text-sm font-medium text-heading">Akta
                            perkawinan dari <span class="text-red-500">*</span></label>
                        <input type="text" id="akta_perkawinan_dari" name="akta_perkawinan_dari"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Akta Perkawinan dari" required
                            @isset($tunjangan) value="{{ old('akta_perkawinan_dari', $tunjangan->akta_perkawinan_dari) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_akta_perkawinan" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            akta perkawinan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_akta_perkawinan" name="no_akta_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor akta perkawinan" required
                                @isset($tunjangan) value="{{ old('no_akta_perkawinan', $tunjangan->no_akta_perkawinan) }}" @endisset />

                            <input type="date" id="tgl_akta_perkawinan" name="tgl_akta_perkawinan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($tunjangan) value="{{ old('tgl_akta_perkawinan', $tunjangan->tgl_akta_perkawinan) }}" @endisset
                                placeholder="Masukan tanggal akta perkawinan" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="akta_kelahiran_dari" class="block mb-2.5 text-sm font-medium text-heading">Akta
                            kelahiran dari <span class="text-red-500">*</span></label>
                        <input type="text" id="akta_kelahiran_dari" name="akta_kelahiran_dari"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Akta kelahiran dari" required
                            @isset($tunjangan) value="{{ old('akta_kelahiran_dari', $tunjangan->akta_kelahiran_dari) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_akta_kelahiran" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            akta kelahiran <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_akta_kelahiran" name="no_akta_kelahiran"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor akta kelahiran" required
                                @isset($tunjangan) value="{{ old('no_akta_kelahiran', $tunjangan->no_akta_kelahiran) }}" @endisset />

                            <input type="date" id="tgl_akta_kelahiran" name="tgl_akta_kelahiran"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($tunjangan) value="{{ old('tgl_akta_kelahiran', $tunjangan->tgl_akta_kelahiran) }}" @endisset
                                placeholder="Masukan tanggal akta kelahiran" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="tebusan" class="block mb-2.5 text-sm font-medium text-heading">Tebusan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tebusan" name="tebusan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tebusan" required
                            @isset($tunjangan) value="{{ old('tebusan', $tunjangan->tebusan) }}" @endisset />
                    </div>
