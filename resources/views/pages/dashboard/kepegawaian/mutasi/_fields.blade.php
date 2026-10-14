                    <div class="mb-5">
                        <label for="instansi_tujuan" class="block mb-2.5 text-sm font-medium text-heading">Instansi
                            Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" id="instansi_tujuan" name="instansi_tujuan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan instansi tujuan" required @isset($instansiValue) value="{{ $instansiValue }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk_mutasi" class="w-1/4 text-sm font-medium text-heading">Nomor dan tgl
                            SK mutasi <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk_mutasi" name="no_sk_mutasi"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor SK" required @isset($noSkValue) value="{{ $noSkValue }}" @endisset />

                            <input type="date" id="tgl_sk_mutasi" name="tgl_sk_mutasi"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal SK mutasi" @isset($tglSkValue) value="{{ $tglSkValue }}" @endisset />
                        </div>
                    </div>
