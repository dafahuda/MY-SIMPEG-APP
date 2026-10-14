                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">Pilih Pegawai</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}" @if ($isEdit) {{ old('pegawai_id', $riwayatPendidikanLanjut->pegawai_id) == $item->id ? 'selected' : '' }} @endif>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="jenjang_pendidikan" class="block mb-2.5 text-sm font-medium text-heading">Jenjang
                            Pendidikan <span class="text-red-500">*</span></label>
                        <select name="jenjang_pendidikan" id="jenjang_pendidikan" class="rounded-lg">
                            <option value="SD" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'SD' ? 'selected' : '' }} @endif>SD</option>
                            <option value="MI" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'MI' ? 'selected' : '' }} @endif>MI</option>
                            <option value="SMP" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'SMP' ? 'selected' : '' }} @endif>SMP</option>
                            <option value="MTS" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'MTS' ? 'selected' : '' }} @endif>MTS</option>
                            <option value="SMK" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'SMK' ? 'selected' : '' }} @endif>SMK</option>
                            <option value="SMA" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'SMA' ? 'selected' : '' }} @endif>SMA</option>
                            <option value="MA" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'MA' ? 'selected' : '' }} @endif>MA</option>
                            <option value="D3" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'D3' ? 'selected' : '' }} @endif>D3</option>
                            <option value="S1" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'S1' ? 'selected' : '' }} @endif>S1</option>
                            <option value="S2" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'S2' ? 'selected' : '' }} @endif>S2</option>
                            <option value="S3" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'S3' ? 'selected' : '' }} @endif>S3</option>
                            <option value="Profesi" @if ($isEdit) {{ old('jenjang_pendidikan', $riwayatPendidikanLanjut->jenjang_pendidikan) == 'Profesi' ? 'selected' : '' }} @endif>Profesi</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nama_sekolah_universitas" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Sekolah / Universitas <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_sekolah_universitas" name="nama_sekolah_universitas"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama sekolah / universitas" required @if ($isEdit) value="{{ old('nama_sekolah_universitas', $riwayatPendidikanLanjut->nama_sekolah_universitas) }}" @endif />
                    </div>
                    <div class="mb-5">
                        <label for="jurusan" class="block mb-2.5 text-sm font-medium text-heading">Jurusan <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="jurusan" name="jurusan"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan Jurusan atau Prodi" required @if ($isEdit) value="{{ old('jurusan', $riwayatPendidikanLanjut->jurusan) }}" @endif />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="thn_mulai" class="w-1/4 text-sm font-medium text-heading">Periode <span
                                class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="thn_mulai" name="thn_mulai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Tahun Mulai" required @if ($isEdit) value="{{ old('thn_mulai', $riwayatPendidikanLanjut->thn_mulai) }}" @endif />

                            <input type="text" id="thn_selesai" name="thn_selesai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required placeholder="Tahun Selesai" @if ($isEdit) value="{{ old('thn_selesai', $riwayatPendidikanLanjut->thn_selesai) }}" @endif />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="status" class="block mb-2.5 text-sm font-medium text-heading">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" id="status" class="rounded-lg">
                            <option value="Tugas Belajar" @if ($isEdit) {{ old('status', $riwayatPendidikanLanjut->status) == 'Tugas Belajar' ? 'selected' : '' }} @endif>Tugas Belajar</option>
                            <option value="Ijin Belajar" @if ($isEdit) {{ old('status', $riwayatPendidikanLanjut->status) == 'Ijin Belajar' ? 'selected' : '' }} @endif>Ijin Belajar</option>
                        </select>
                    </div>
