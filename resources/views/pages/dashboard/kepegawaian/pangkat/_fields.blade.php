                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai @unless (isset($pangkat))<span
                                class="text-red-500">*</span>@endunless</label>
                        <select name="pegawai_id" id="pegawai_id" @unless (isset($pangkat)) class="rounded-lg" @endunless>
                            <option value="">Pilih Pegawai</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}" @isset($pangkat) {{ old('pegawai_id', $pangkat->pegawai_id) == $item->id ? 'selected' : '' }} @endisset>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="master_pangkat_id" class="block mb-2.5 text-sm font-medium text-heading">Pangkat
                            @unless (isset($pangkat))<span class="text-red-500">*</span>@endunless</label>
                        <div x-data="pangkatComponent()">
                            <div class="flex gap-2">
                                <select name="master_pangkat_id" id="master_pangkat_id"
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm">
                                    @foreach ($masterPangkat as $item)
                                        <option value="{{ $item->id }}" @isset($pangkat) {{ old('master_pangkat_id', $pangkat->master_pangkat_id) == $item->id ? 'selected' : '' }} @endisset>{{ $item->nama_pangkat }}</option>
                                    @endforeach
                                </select>

                                <button type="button" @click="openPangkat = true"
                                    class="bg-blue-500 text-white px-3 py-1 rounded shadow hover:bg-blue-600 transition shrink-0">
                                    + ADD PAN
                                </button>

                                <div x-show="openPangkat" class="fixed inset-0 z-50 overflow-y-auto"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-10"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-10"
                                    class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                                    <div class="flex items-center justify-center min-h-screen p-4">
                                        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"
                                            @click="openPangkat = false">
                                        </div>
                                        <div class="relative bg-white rounded-lg shadow-xl sm:max-w-4xl sm:w-full">
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <h3 class="text-lg font-bold text-blue-600">Master Nama Pangkat</h3>
                                                <button @unless (isset($pangkat)) type="button" @endunless @click="openPangkat = false" class="text-2xl">&times;</button>
                                            </div>
                                            <div class="p-6">

                                                <!-- FORM TAMBAH -->
                                                <div class="flex gap-2 mb-4">
                                                    <input type="text" x-model="nama_pangkat"
                                                        class="border rounded px-3 py-2 w-full"
                                                        placeholder="Masukan nama pangkat">

                                                    <button type="button" @click="addPangkat"
                                                        class="bg-blue-500 text-white px-4 rounded">Simpan</button>
                                                </div>

                                                <!-- LIST DATA -->
                                                <table class="w-full text-sm border-collapse border border-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="border p-2 text-left w-12">No</th>
                                                            <th class="border p-2 text-left">Nama Pangkat</th>
                                                            <th class="border p-2 text-left w-40">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-for="(item, index) in listPangkat"
                                                            :key="item.id">
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="border p-2 text-center" x-text="index + 1">
                                                                </td>
                                                                <td class="border p-2">
                                                                    <span x-show="editId !== item.id"
                                                                        x-text="item.nama_pangkat"></span>
                                                                    <input x-show="editId === item.id" type="text"
                                                                        x-model="editNama"
                                                                        class="border rounded px-2 py-1 w-full text-sm">
                                                                </td>
                                                                <td class="border p-2 space-x-2">
                                                                    <template x-if="editId !== item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="startEdit(item)"
                                                                                class="inline-block bg-blue-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-blue-600">Ubah</button>
                                                                            <button type="button"
                                                                                @click="deletePangkat(item.id)"
                                                                                class="inline-block bg-red-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-red-700">Hapus</button>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="editId === item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="saveEdit(item)"
                                                                                class="inline-block bg-green-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-green-600">Simpan</button>
                                                                            <button type="button" @click="cancelEdit()"
                                                                                class="inline-block bg-gray-400 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-gray-500">
                                                                                Batal
                                                                            </button>
                                                                        </span>
                                                                    </template>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="master_golongan_id" class="block mb-2.5 text-sm font-medium text-heading">Golongan
                            @unless (isset($pangkat))<span class="text-red-500">*</span>@endunless</label>
                        <div x-data="golonganComponent()">
                            <div class="flex gap-2">
                                <select name="master_golongan_id" id="master_golongan_id"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm">
                                    @foreach ($masterGolongan as $item)
                                        <option value="{{ $item->id }}" @isset($pangkat) {{ old('master_golongan_id', $pangkat->master_golongan_id) == $item->id ? 'selected' : '' }} @endisset>{{ $item->nama_golongan }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="openGolongan = true"
                                    class="bg-blue-500 text-white px-3 py-1 rounded shadow hover:bg-blue-600 transition shrink-0">
                                    + ADD GOL
                                </button>

                                <div x-show="openGolongan" class="fixed inset-0 z-50 overflow-y-auto"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-10"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-10"
                                    class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                                    <div class="flex items-center justify-center min-h-screen p-4">
                                        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"
                                            @click="openGolongan = false">
                                        </div>
                                        <div class="relative bg-white rounded-lg shadow-xl sm:max-w-4xl sm:w-full">
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <h3 class="text-lg font-bold text-green-600">Master Data Golongan</h3>
                                                <button @unless (isset($pangkat)) type="button" @endunless @click="openGolongan = false"
                                                    class="text-2xl">&times;</button>
                                            </div>
                                            <div class="p-6">

                                                <!-- FORM TAMBAH -->
                                                <div class="flex gap-2 mb-4">
                                                    <input type="text" x-model="nama_golongan"
                                                        class="border rounded px-3 py-2 w-full"
                                                        placeholder="Masukan nama eselon">

                                                    <button type="button" @click="addGolongan"
                                                        class="bg-blue-500 text-white px-4 rounded">Simpan</button>
                                                </div>

                                                <!-- LIST DATA -->
                                                <table class="w-full text-sm border-collapse border border-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="border p-2 text-left w-12">No</th>
                                                            <th class="border p-2 text-left">Nama Golongan</th>
                                                            <th class="border p-2 text-left w-40">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-for="(item, index) in listGolongan"
                                                            :key="item.id">
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="border p-2 text-center" x-text="index + 1">
                                                                </td>
                                                                <td class="border p-2">
                                                                    <span x-show="editId !== item.id"
                                                                        x-text="item.nama_golongan"></span>
                                                                    <input x-show="editId === item.id" type="text"
                                                                        x-model="editNama"
                                                                        class="border rounded px-2 py-1 w-full text-sm">
                                                                </td>
                                                                <td class="border p-2 space-x-2">
                                                                    <template x-if="editId !== item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="startEdit(item)"
                                                                                class="inline-block bg-blue-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-blue-600">Ubah</button>
                                                                            <button type="button"
                                                                                @click="deleteGolongan(item.id)"
                                                                                class="inline-block bg-red-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-red-700">Hapus</button>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="editId === item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="saveEdit(item)"
                                                                                class="inline-block bg-green-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-green-600">Simpan</button>
                                                                            <button type="button"
                                                                                @click="cancelEdit()"
                                                                                class="inline-block bg-gray-400 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-gray-500">
                                                                                Batal
                                                                            </button>
                                                                        </span>
                                                                    </template>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="jenis_pangkat" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Pangkat @unless (isset($pangkat))<span class="text-red-500">*</span>@endunless</label>
                        <input type="text" id="jenis_pangkat" name="jenis_pangkat"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan jenis pangkat" required @isset($pangkat) value="{{ old('jenis_pangkat', $pangkat->jenis_pangkat) }}" @endisset />
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tmt_pangkat_mulai" class="w-1/4 text-sm font-medium text-heading">TMT
                            Pangkat @unless (isset($pangkat))<span class="text-red-500">*</span>@endunless</label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="tmt_pangkat_mulai" name="tmt_pangkat_mulai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Dari" required @isset($pangkat) value="{{ old('tmt_pangkat_mulai', $pangkat->tmt_pangkat_mulai) }}" @endisset />

                            <input type="date" id="tmt_pangkat_selesai" name="tmt_pangkat_selesai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($pangkat) value="{{ old('tmt_pangkat_selesai', $pangkat->tmt_pangkat_selesai) }}" @endisset />
                        </div>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            SK @unless (isset($pangkat))<span class="text-red-500">*</span>@endunless</label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk" name="no_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor SK" required @isset($pangkat) value="{{ old('no_sk', $pangkat->no_sk) }}" @endisset />

                            <input type="date" id="tgl_sk" name="tgl_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs"
                                required @isset($pangkat) value="{{ old('tgl_sk', $pangkat->tgl_sk) }}" @endisset />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="pejabat_pengesah_sk" class="block mb-2.5 text-sm font-medium text-heading">Pejabat
                            Pengesah SK @unless (isset($pangkat))<span class="text-red-500">*</span>@endunless</label>
                        <input type="text" id="pejabat_pengesah_sk" name="pejabat_pengesah_sk"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Diterbikan oleh" required @isset($pangkat) value="{{ old('pejabat_pengesah_sk', $pangkat->pejabat_pengesah_sk) }}" @endisset />
                    </div>
