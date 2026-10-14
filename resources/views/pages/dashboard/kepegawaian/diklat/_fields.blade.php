                    <div class="mb-5">
                        <label for="nama_diklat" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Diklat <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_diklat" name="nama_diklat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama diklat" required
                            @isset($fieldValues['nama_diklat']) value="{{ $fieldValues['nama_diklat'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="jumlah_jam" class="block mb-2.5 text-sm font-medium text-heading">Jumlah Jam <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jumlah_jam" name="jumlah_jam"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jumlah jam diklat" required
                            @isset($fieldValues['jumlah_jam']) value="{{ $fieldValues['jumlah_jam'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="penyelenggara" class="block mb-2.5 text-sm font-medium text-heading">Penyelanggara
                            <span class="text-red-500">*</span></label>
                        <input type="text" id="penyelenggara" name="penyelenggara"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan penyelenggara diklat" required
                            @isset($fieldValues['penyelenggara']) value="{{ $fieldValues['penyelenggara'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="tempat" class="block mb-2.5 text-sm font-medium text-heading">Tempat <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tempat" name="tempat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tempat diklat" required
                            @isset($fieldValues['tempat']) value="{{ $fieldValues['tempat'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="angkatan" class="block mb-2.5 text-sm font-medium text-heading">Angkatan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="angkatan" name="angkatan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan angkatan diklat" required
                            @isset($fieldValues['angkatan']) value="{{ $fieldValues['angkatan'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="tahun" class="block mb-2.5 text-sm font-medium text-heading">Tahun <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="tahun" name="tahun"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan tahun diklat" required
                            @isset($fieldValues['tahun']) value="{{ $fieldValues['tahun'] }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sttpp" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            sttpp <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sttpp" name="no_sttpp"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor STTPP" required
                            @isset($fieldValues['no_sttpp']) value="{{ $fieldValues['no_sttpp'] }}" @endisset />

                            <input type="date" id="tgl_sttpp" name="tgl_sttpp"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Masukan tanggal STTPP"
                            @isset($fieldValues['tgl_sttpp']) value="{{ $fieldValues['tgl_sttpp'] }}" @endisset />
                        </div>
                    </div>
