                    <div class="flex items-center mb-5">
                        <label for="no_surat_cuti" class="w-1/4 text-sm font-medium text-heading">Nomor dan Surat
                            Cuti <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_surat_cuti" name="no_surat_cuti"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor surat" required
                                @isset($cuti) value="{{ old('no_surat_cuti', $cuti->no_surat_cuti) }}" @endisset />

                            <input type="date" id="tgl_surat_cuti" name="tgl_surat_cuti"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($cuti) value="{{ old('tgl_surat_cuti', $cuti->tgl_surat_cuti) }}" @endisset
                                placeholder="Masukan tanggal selesai pelaksanaan cuti" />
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="pelaksanaan_cuti_mulai" class="w-1/4 text-sm font-medium text-heading">Pelaksanaan
                            Cuti <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="pelaksanaan_cuti_mulai" name="pelaksanaan_cuti_mulai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tanggal mulai pelaksanaan cuti"
                                alt="tanggal mulai pelaksanaan cuti" required
                                @isset($cuti) value="{{ old('pelaksanaan_cuti_mulai', $cuti->pelaksanaan_cuti_mulai) }}" @endisset />

                            <input type="date" id="pelaksanaan_cuti_selesai" name="pelaksanaan_cuti_selesai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($cuti) value="{{ old('pelaksanaan_cuti_selesai', $cuti->pelaksanaan_cuti_selesai) }}" @endisset
                                placeholder="Masukan tanggal selesai pelaksanaan cuti" alt="tanggal selesai cuti" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="durasi_cuti" class="block mb-2.5 text-sm font-medium text-heading">Durasi
                            Cuti <span class="text-red-500">*</span></label>
                        <input type="text" id="durasi_cuti" name="durasi_cuti"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan durasi cuti" required
                            @isset($cuti) value="{{ old('durasi_cuti', $cuti->durasi_cuti) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="ketentuan_a" class="block mb-2.5 text-sm font-medium text-heading">Ketentuan
                            A <span class="text-red-500">*</span></label>
                        <input type="text" id="ketentuan_a" name="ketentuan_a"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan ketentuan a" required
                            @isset($cuti) value="{{ old('ketentuan_a', $cuti->ketentuan_a) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="ketentuan_b" class="block mb-2.5 text-sm font-medium text-heading">Ketentuan
                            B <span class="text-red-500">*</span></label>
                        <input type="text" id="ketentuan_b" name="ketentuan_b"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan ketentuan b" required
                            @isset($cuti) value="{{ old('ketentuan_b', $cuti->ketentuan_b) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="ketentuan_c" class="block mb-2.5 text-sm font-medium text-heading">Ketentuan
                            C <span class="text-red-500">*</span></label>
                        <input type="text" id="ketentuan_c" name="ketentuan_c"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan ketentuan c" required
                            @isset($cuti) value="{{ old('ketentuan_c', $cuti->ketentuan_c) }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="tebusan" class="block mb-2.5 text-sm font-medium text-heading">Tebusan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tebusan" name="tebusan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tebusan" required @isset($cuti) value="{{ old('tebusan', $cuti->tebusan) }}" @endisset />
                    </div>
