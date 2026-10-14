                    <div class="mb-5">
                        <label for="nik" class="block mb-2.5 text-sm font-medium text-heading">No KTP / NIK <span
                                class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nik" name="nik"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nik Orang Tua" required
                            @isset($fieldValues['nik']) value="{{ $fieldValues['nik'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="nama" class="block mb-2.5 text-sm font-medium text-heading">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama orang tua" required
                            @isset($fieldValues['nama']) value="{{ $fieldValues['nama'] }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_lahir" class="w-1/4 text-sm font-medium text-heading">Tempat, Tanggal
                            Lahir <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_lahir" name="tempat_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat lahir" required
                                @isset($fieldValues['tempat_lahir']) value="{{ $fieldValues['tempat_lahir'] }}" @endisset />

                            <input type="date" id="tgl_lahir" name="tgl_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                @isset($fieldValues['tgl_lahir']) value="{{ $fieldValues['tgl_lahir'] }}" @endisset required />
                        </div>
                    </div>
