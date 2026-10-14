                    <div class="flex items-center mb-5">
                        <label for="periode_nilai_dari" class="w-1/4 text-sm font-medium text-heading">Periode
                            Penilaian <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="periode_nilai_dari" name="periode_nilai_dari"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Dari" required @if(array_key_exists('periode_nilai_dari', $fieldValues)) value="{{ $fieldValues['periode_nilai_dari'] }}" @endif />

                            <input type="date" id="periode_nilai_sampai" name="periode_nilai_sampai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Sampai" @if(array_key_exists('periode_nilai_sampai', $fieldValues)) value="{{ $fieldValues['periode_nilai_sampai'] }}" @endif />

                            <input type="text" id="tahun_periode" name="tahun_periode"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Tahun" @if(array_key_exists('tahun_periode', $fieldValues)) value="{{ $fieldValues['tahun_periode'] }}" @endif />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="nama_pejabat_nilai" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Pejabat penilai <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_pejabat_nilai" name="nama_pejabat_nilai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama pejabat penilai" required @if(array_key_exists('nama_pejabat_nilai', $fieldValues)) value="{{ $fieldValues['nama_pejabat_nilai'] }}" @endif />
                    </div>
                    <div class="mb-5">
                        <label for="nama_atasan_pejabat_penilai"
                            class="block mb-2.5 text-sm font-medium text-heading">Nama Atasan Pejabat penilai <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="nama_atasan_pejabat_penilai" name="nama_atasan_pejabat_penilai"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama atasan pejabat penilai" required @if(array_key_exists('nama_atasan_pejabat_penilai', $fieldValues)) value="{{ $fieldValues['nama_atasan_pejabat_penilai'] }}" @endif />
                    </div>
