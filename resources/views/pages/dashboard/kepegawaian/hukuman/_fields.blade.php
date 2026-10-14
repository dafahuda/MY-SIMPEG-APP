                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">Pilih Pegawai</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}" @isset($hukuman)
                                    {{ old('pegawai_id', $hukuman->pegawai_id) == $item->id ? 'selected' : '' }}
                                    @endisset>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="pelanggaran_yg_dilakukan"
                            class="block mb-2.5 text-sm font-medium text-heading">Pelanggaran yang dilakukan <span
                                class="text-red-500">*</span></label>

                        <textarea id="pelanggaran_yg_dilakukan" name="pelanggaran_yg_dilakukan" rows="4" cols="50"
                            placeholder="Masukan pelanggaran yang dilakukan" class="rounded-lg">@isset($hukuman){{ old('pelanggaran_yg_dilakukan', $hukuman->pelanggaran_yg_dilakukan) }}@endisset</textarea>
                    </div>
                    <div class="mb-5">
                        <label for="tingkat_hukuman" class="block mb-2.5 text-sm font-medium text-heading">Tingkat
                            Hukuman <span class="text-red-500">*</span></label>
                        <select name="tingkat_hukuman" id="tingkat_hukuman" class="rounded-lg">
                            <option value="Ringan" @isset($hukuman) {{ $hukuman->tingkat_hukuman == 'Ringan' ? 'selected' : '' }} @endisset>Ringan
                            </option>
                            <option value="Sedang" @isset($hukuman) {{ $hukuman->tingkat_hukuman == 'Sedang' ? 'selected' : '' }} @endisset>Sedang
                            </option>
                            <option value="Berat" @isset($hukuman) {{ $hukuman->tingkat_hukuman == 'Berat' ? 'selected' : '' }} @endisset>Berat
                            </option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_hukuman" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Hukuman <span class="text-red-500">*</span></label>
                        <select name="jenis_hukuman" id="jenis_hukuman" class="rounded-lg">
                            <option value="Teguran Lisan"
                                @isset($hukuman) {{ $hukuman->jenis_hukuman == 'Teguran Lisan' ? 'selected' : '' }} @endisset>Teguran Lisan
                            </option>
                            <option value="Teguran Tertulis"
                                @isset($hukuman) {{ $hukuman->jenis_hukuman == 'Teguran Tertulis' ? 'selected' : '' }} @endisset>Teguran Tertulis
                            </option>
                            <option value="Tunda Kenaikan Berkala"
                                @isset($hukuman) {{ $hukuman->jenis_hukuman == 'Tunda Kenaikan Berkala' ? 'selected' : '' }} @endisset>Tunda
                                Kenaikan Berkala</option>
                            <option value="Tunda Kenaikan Pangkat"
                                @isset($hukuman) {{ $hukuman->jenis_hukuman == 'Tunda Kenaikan Pangkat' ? 'selected' : '' }} @endisset>Tunda
                                Kenaikan Pangkat</option>
                            <option value="Pemberhentian"
                                @isset($hukuman) {{ $hukuman->jenis_hukuman == 'Pemberhentian' ? 'selected' : '' }} @endisset>Pemberhentian
                            </option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="isi_teguran" class="block mb-2.5 text-sm font-medium text-heading">Isi
                            teguran <span class="text-red-500">*</span></label>

                        <textarea id="isi_teguran" name="isi_teguran" rows="4" cols="50" class="rounded-lg">@isset($hukuman){{ old('isi_teguran', $hukuman->isi_teguran) }}@endisset</textarea>
                    </div>
                    <div class="mb-5">
                        <label for="pejabat_pengesahan_sk_hukuman"
                            class="block mb-2.5 text-sm font-medium text-heading">Pejabat Pengesahan Hukuman <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pejabat_pengesahan_sk_hukuman" name="pejabat_pengesahan_sk_hukuman"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pejabat pengesahan hukuman" required
                            @isset($hukuman) value="{{ old('pejabat_pengesahan_sk_hukuman', $hukuman->pejabat_pengesahan_sk_hukuman) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            pengesahan SK <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk" name="no_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor SK" required @isset($hukuman) value="{{ old('no_sk', $hukuman->no_sk) }}" @endisset />

                            <input type="date" id="tgl_pengesahan_sk" name="tgl_pengesahan_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal SK"
                                @isset($hukuman) value="{{ old('tgl_pengesahan_sk', $hukuman->tgl_pengesahan_sk) }}" @endisset />
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tmt_hukuman_mulai" class="w-1/4 text-sm font-medium text-heading">TMT
                            Hukuman <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="tmt_hukuman_mulai" name="tmt_hukuman_mulai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Dari" required
                                @isset($hukuman) value="{{ old('tmt_hukuman_mulai', $hukuman->tmt_hukuman_mulai) }}" @endisset />

                            <input type="date" id="tmt_hukuman_pemulihan" name="tmt_hukuman_pemulihan"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Sampai"
                                @isset($hukuman) value="{{ old('tmt_hukuman_pemulihan', $hukuman->tmt_hukuman_pemulihan) }}" @endisset />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="pejabat_pemulihan_hukuman"
                            class="block mb-2.5 text-sm font-medium text-heading">Pejabat Pemulihan Hukuman <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pejabat_pemulihan_hukuman" name="pejabat_pemulihan_hukuman"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan pejabat pemulihan hukuman" required
                            @isset($hukuman) value="{{ old('pejabat_pemulihan_hukuman', $hukuman->pejabat_pemulihan_hukuman) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            pemulihan hukuman <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_pemulihan_hukuman" name="no_pemulihan_hukuman"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor pemulihan hukuman" required
                                @isset($hukuman) value="{{ old('no_pemulihan_hukuman', $hukuman->no_pemulihan_hukuman) }}" @endisset />

                            <input type="date" id="tgl_pemulihan_hukuman" name="tgl_pemulihan_hukuman"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal pemulihan hukuman"
                                @isset($hukuman) value="{{ old('tgl_pemulihan_hukuman', $hukuman->tgl_pemulihan_hukuman) }}" @endisset />
                        </div>
                    </div>
