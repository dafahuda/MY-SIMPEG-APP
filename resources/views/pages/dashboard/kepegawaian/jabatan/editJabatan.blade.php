<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit data jabatan</h1>
            </div>

        </div>

        <div x-data="{}"
            class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 mb-5">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="/kepegawaian/jabatan/edit_data_jabatan/{{ $jabatan->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-5">
                        <label for="pegawai_id" class="block mb-2.5 text-sm font-medium text-heading">Pegawai <span
                                class="text-red-500">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="rounded-lg">
                            <option value="">--Pilih Pegawai --</option>
                            @foreach ($pegawai as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('pegawai_id', $jabatan->pegawai_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="master_jabatan_id" class="block mb-2.5 text-sm font-medium text-heading">Jabatan
                            <span class="text-red-500">*</span></label>
                        <div x-data="jabatanComponent()">
                            <div class="flex gap-2">
                                <select name="master_jabatan_id" id="master_jabatan_id"
                                    class="flex-1 rounded-lg border-gray-300 shadow-sm">
                                    @foreach ($masterJabatan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('master_jabatan_id', $jabatan->master_jabatan_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_jabatan }}</option>
                                    @endforeach
                                </select>

                                <button type="button" @click="openJabatan = true"
                                    class="bg-orange-500 text-white px-3 py-1 rounded shadow hover:bg-orange-600 transition shrink-0">
                                    + ADD JAB
                                </button>

                                <div x-show="openJabatan" class="fixed inset-0 z-50 overflow-y-auto"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-10"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-10"
                                    class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                                    <div class="flex items-center justify-center min-h-screen p-4">
                                        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"
                                            @click="openJabatan = false">
                                        </div>
                                        <div class="relative bg-white rounded-lg shadow-xl sm:max-w-4xl sm:w-full">
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <h3 class="text-lg font-bold text-blue-600">Master Nama Jabatan</h3>
                                                <button @click="openJabatan = false" class="text-2xl">&times;</button>
                                            </div>
                                            <div class="p-6">

                                                <!-- FORM TAMBAH -->
                                                <div class="flex gap-2 mb-4">
                                                    <input type="text" x-model="nama_jabatan"
                                                        class="border rounded px-3 py-2 w-full"
                                                        placeholder="Masukan nama jabatan">

                                                    <button type="button" @click="addJabatan"
                                                        class="bg-blue-500 text-white px-4 rounded">
                                                        Save
                                                    </button>
                                                </div>

                                                <!-- LIST DATA -->
                                                <table class="w-full text-sm border-collapse border border-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="border p-2 text-left w-12">No</th>
                                                            <th class="border p-2 text-left">Nama Jabatan</th>
                                                            <th class="border p-2 text-left w-40">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-for="(item, index) in listJabatan"
                                                            :key="item.id">
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="border p-2 text-center" x-text="index + 1">
                                                                </td>
                                                                <td class="border p-2">
                                                                    <span x-show="editId !== item.id"
                                                                        x-text="item.nama_jabatan"></span>
                                                                    <input x-show="editId === item.id" type="text"
                                                                        x-model="editNama"
                                                                        class="border rounded px-2 py-1 w-full text-sm">
                                                                </td>
                                                                <td class="border p-2 space-x-2">
                                                                    <template x-if="editId !== item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="startEdit(item)"
                                                                                class="inline-block bg-blue-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-blue-600">
                                                                                Edit
                                                                            </button>
                                                                            <button type="button"
                                                                                @click="deleteJabatan(item.id)"
                                                                                class="inline-block bg-red-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-red-700">
                                                                                Delete
                                                                            </button>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="editId === item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="saveEdit(item)"
                                                                                class="inline-block bg-green-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-green-600">
                                                                                Save
                                                                            </button>
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
                        <label for="master_eselon_id" class="block mb-2.5 text-sm font-medium text-heading">Eselon <span
                                class="text-red-500">*</span></label>
                        <div x-data="eselonComponent()">
                            <div class="flex gap-2">
                                <select name="master_eselon_id" id="master_eselon_id"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm">
                                    @foreach ($masterEselon as $item)
                                        <option value="{{ $item->id }}"
                                            value="{{ old('master_eselon_id', $jabatan->master_eselon_id) == $item->id ? 'selected' : '' }}">
                                            {{ $item->nama_eselon }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="openEselon = true"
                                    class="bg-orange-500 text-white px-3 py-1 rounded shadow hover:bg-orange-600 transition shrink-0">
                                    + ADD ESL
                                </button>

                                <div x-show="openEselon" class="fixed inset-0 z-50 overflow-y-auto"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-10"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-10"
                                    class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                                    <div class="flex items-center justify-center min-h-screen p-4">
                                        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"
                                            @click="openEselon = false">
                                        </div>
                                        <div class="relative bg-white rounded-lg shadow-xl sm:max-w-4xl sm:w-full">
                                            <div class="px-4 py-3 border-b flex justify-between items-center">
                                                <h3 class="text-lg font-bold text-green-600">Master Data Eselon</h3>
                                                <button @click="openEselon = false" class="text-2xl">&times;</button>
                                            </div>
                                            <div class="p-6">

                                                <!-- FORM TAMBAH -->
                                                <div class="flex gap-2 mb-4">
                                                    <input type="text" x-model="nama_eselon"
                                                        class="border rounded px-3 py-2 w-full"
                                                        placeholder="Masukan nama eselon">

                                                    <button type="button" @click="addEselon"
                                                        class="bg-blue-500 text-white px-4 rounded">
                                                        Save
                                                    </button>
                                                </div>

                                                <!-- LIST DATA -->
                                                <table class="w-full text-sm border-collapse border border-gray-300">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="border p-2 text-left w-12">No</th>
                                                            <th class="border p-2 text-left">Nama Eselon</th>
                                                            <th class="border p-2 text-left w-40">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-for="(item, index) in listEselon"
                                                            :key="item.id">
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="border p-2 text-center" x-text="index + 1">
                                                                </td>
                                                                <td class="border p-2">
                                                                    <span x-show="editId !== item.id"
                                                                        x-text="item.nama_eselon"></span>
                                                                    <input x-show="editId === item.id" type="text"
                                                                        x-model="editNama"
                                                                        class="border rounded px-2 py-1 w-full text-sm">
                                                                </td>
                                                                <td class="border p-2 space-x-2">
                                                                    <template x-if="editId !== item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="startEdit(item)"
                                                                                class="inline-block bg-blue-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-blue-600">
                                                                                Edit
                                                                            </button>
                                                                            <button type="button"
                                                                                @click="deleteEselon(item.id)"
                                                                                class="inline-block bg-red-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-red-700">
                                                                                Delete
                                                                            </button>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="editId === item.id">
                                                                        <span>
                                                                            <button type="button"
                                                                                @click="saveEdit(item)"
                                                                                class="inline-block bg-green-500 text-white rounded-lg shadow-lg py-2 px-3 hover:bg-green-600">
                                                                                Save
                                                                            </button>
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
                        <label for="jenis_jabatan" class="block mb-2.5 text-sm font-medium text-heading">Jenis
                            Jabatan <span class="text-red-500">*</span></label>
                        <select name="jenis_jabatan" id="jenis_jabatan" class="rounded-lg">
                            <option value="Jabatan Struktural"
                                {{ $jabatan->jenis_jabatan == 'Jabatan Struktural' ? 'selected' : '' }}>Jabatan
                                Struktural</option>
                            <option value="Jabatan Fungsional Tertentu"
                                {{ $jabatan->jenis_jabatan == 'Jabatan Fungsional Tertentu' ? 'selected' : '' }}>
                                Jabatan Fungsional Tertentu</option>
                            <option value="Jabatan Fungsional Umum"
                                {{ $jabatan->jenis_jabatan == 'Jabatan Fungsional Umum' ? 'selected' : '' }}>Jabatan
                                Fungsional Umum</option>
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="tmt_jabatan_mulai" class="w-1/4 text-sm font-medium text-heading">TMT
                            Jabatan <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="date" id="tmt_jabatan_mulai" name="tmt_jabatan_mulai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Dari" required
                                value="{{ old('tmt_jabatan_mulai', $jabatan->tmt_jabatan_mulai) }}" />

                            <input type="date" id="tmt_jabatan_selesai" name="tmt_jabatan_selesai"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tmt_jabatan_selesai', $jabatan->tmt_jabatan_selesai) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="periode" class="block mb-2.5 text-sm font-medium text-heading">Periode <span
                                class="text-red-500">*</span></label>
                        <select name="periode" id="periode" class="rounded-lg">
                            <option value="-" {{ $jabatan->periode == '-' ? 'selected' : '' }}>-</option>
                            <option value="I" {{ $jabatan->periode == 'I' ? 'selected' : '' }}>I</option>
                            <option value="II" {{ $jabatan->periode == 'II' ? 'selected' : '' }}>II</option>
                            <option value="Sudah Selesai"
                                {{ $jabatan->periode == 'Sudah Selesai' ? 'selected' : '' }}>Sudah Selesai</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="tahun_ke" class="block mb-2.5 text-sm font-medium text-heading">Tahun Ke <span
                                class="text-red-500">*</span></label>
                        <select name="tahun_ke" id="tahun_ke" class="rounded-lg">
                            <option value="-" {{ $jabatan->tahun_ke == '-' ? 'selected' : '' }}>-</option>
                            <option value="1" {{ $jabatan->periode == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $jabatan->periode == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ $jabatan->periode == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ $jabatan->periode == '4' ? 'selected' : '' }}>4</option>
                            <option value="Sudah Selesai"
                                {{ $jabatan->periode == 'Sudah Selesai' ? 'selected' : '' }}>Sudah Selesai</option>
                        </select>
                    </div>
                    <div class="flex items-center mb-5">
                        <label for="no_sk" class="w-1/4 text-sm font-medium text-heading">Nomor dan tanggal
                            SK <span class="text-red-500">*</span></label>
                        <div class="flex w-3/4 gap-4">
                            <input type="text" id="no_sk" name="no_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan nomor SK" required
                                value="{{ old('no_sk', $jabatan->no_sk) }}" />

                            <input type="date" id="tgl_sk" name="tgl_sk"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required value="{{ old('tgl_sk', $jabatan->tgl_sk) }}" />
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="terbit" class="block mb-2.5 text-sm font-medium text-heading">Diterbitkan
                            oleh <span class="text-red-500">*</span></label>
                        <input type="text" id="terbit" name="terbit"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Diterbikan oleh" required value="{{ old('terbit', $jabatan->terbit) }}" />
                    </div>
                    <button type="button"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none confirm-save"
                        data-title="Simpan Perubahan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data jabatan ini?">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/jabatan"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function jabatanComponent() {
        return {
            openJabatan: false,
            nama_jabatan: '',
            editId: null,
            editNama: '',
            listJabatan: @json($masterJabatan),

            async addJabatan() {
                if (!this.nama_jabatan) return;

                let res = await fetch('/kepegawaian/master_jabatan/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_jabatan: this.nama_jabatan
                    })
                });

                let data = await res.json();

                this.listJabatan.push(data);
                this.nama_jabatan = '';
            },

            startEdit(item) {
                this.editId = item.id;
                this.editNama = item.nama_jabatan;
            },

            cancelEdit() {
                this.editId = null;
                this.editNama = '';
            },

            async saveEdit(item) {
                if (!this.editNama) return;

                let res = await fetch(`/kepegawaian/master_jabatan/update_master_jabatan/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_jabatan: this.editNama
                    })
                });

                if (res.ok) {
                    let data = await res.json();
                    let idx = this.listJabatan.findIndex(j => j.id === item.id);
                    if (idx !== -1) this.listJabatan[idx].nama_jabatan = data.nama_jabatan;
                    this.editId = null;
                    this.editNama = '';
                } else {
                    alert('Gagal mengubah data');
                }
            },

            async deleteJabatan(id) {
                showConfirm({
                    type: 'danger',
                    title: 'Hapus Master Jabatan',
                    message: 'Data master jabatan yang dihapus tidak dapat dikembalikan. Yakin ingin menghapus?',
                    confirmText: 'Ya, Hapus',
                    callback: async () => {
                        let res = await fetch(
                            `/kepegawaian/master_jabatan/delete_master_jabatan/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });

                        if (res.ok) {
                            this.listJabatan = this.listJabatan.filter(item => item.id !== id);
                        } else {
                            alert('Gagal menghapus data');
                        }
                    }
                });
            }
        }
    }

    function eselonComponent() {
        return {
            openEselon: false,
            nama_eselon: '',
            editId: null,
            editNama: '',
            listEselon: @json($masterEselon),

            async addEselon() {
                if (!this.nama_eselon) return;

                let res = await fetch('/kepegawaian/master_eselon/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_eselon: this.nama_eselon
                    })
                });

                let data = await res.json();

                this.listEselon.push(data);
                this.nama_eselon = '';
            },

            startEdit(item) {
                this.editId = item.id;
                this.editNama = item.nama_eselon;
            },

            cancelEdit() {
                this.editId = null;
                this.editNama = '';
            },

            async saveEdit(item) {
                if (!this.editNama) return;

                let res = await fetch(`/kepegawaian/master_eselon/edit_master_eselon/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_eselon: this.editNama
                    })
                });

                if (res.ok) {
                    let data = await res.json();
                    let idx = this.listEselon.findIndex(e => e.id === item.id);
                    if (idx !== -1) this.listEselon[idx].nama_eselon = data.nama_eselon;
                    this.editId = null;
                    this.editNama = '';
                } else {
                    alert('Gagal mengubah data');
                }
            },

            async deleteEselon(id) {
                showConfirm({
                    type: 'danger',
                    title: 'Hapus Master Eselon',
                    message: 'Data master eselon yang dihapus tidak dapat dikembalikan. Yakin ingin menghapus?',
                    confirmText: 'Ya, Hapus',
                    callback: async () => {
                        let res = await fetch(`/kepegawaian/master_eselon/delete_master_eselon/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            this.listEselon = this.listEselon.filter(item => item.id !== id);
                        } else {
                            alert('Gagal menghapus data');
                        }
                    }
                });
            }
        }
    }
</script>
