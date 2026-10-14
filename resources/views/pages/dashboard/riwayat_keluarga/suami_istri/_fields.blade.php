@if ($section === 'identity')
                    <div class="mb-5">
                        <label for="no_ktp_nik" class="block mb-2.5 text-sm font-medium text-heading">No KTP / NIK <span
                                class="text-red-500">*</span>
                        </label>
                        <input type="text" id="no_ktp_nik" name="no_ktp_nik"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nik suami / istri" required @isset($fieldValues) value="{{ $fieldValues['no_ktp_nik'] }}" @endisset />
                    </div>
                    <div class="mb-5">
                        <label for="nama" class="block mb-2.5 text-sm font-medium text-heading">Nama Suami
                            Istri <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama suami / istri" required @isset($fieldValues) value="{{ $fieldValues['nama'] }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tempat_lahir" class="w-1/4 text-sm font-medium text-heading">Tempat, Tanggal
                            Lahir <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="tempat_lahir" name="tempat_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan tempat lahir" required @isset($fieldValues) value="{{ $fieldValues['tempat_lahir'] }}" @endisset />

                            <input type="date" id="tgl_lahir" name="tgl_lahir"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($fieldValues) value="{{ $fieldValues['tgl_lahir'] }}" @endisset />
                        </div>
                    </div>
@elseif ($section === 'pekerjaan')
                    <div class="mb-5">
                        <label for="pekerjaan" class="block mb-2.5 text-sm font-medium text-heading">Pekerjaan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="pekerjaan" name="pekerjaan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Pekerjaan Suami / Istri" required @isset($fieldValues) value="{{ $fieldValues['pekerjaan'] }}" @endisset />
                    </div>
@endif
