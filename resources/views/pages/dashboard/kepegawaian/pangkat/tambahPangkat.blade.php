<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form tambah data pangkat</h1>
            </div>

        </div>

        <div x-data="{}"
            class="bg-white dark:bg-gray-800 shadow-sm rounded-sm border border-gray-200 dark:border-gray-700 p-5">
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
                <form action="/kepegawaian/pangkat/tambah_data_pangkat" method="POST">
                    @csrf
                    @include('pages.dashboard.kepegawaian.pangkat._fields')
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-blue-500 shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Simpan</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/pangkat"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function pangkatComponent() {
        return {
            openPangkat: false,
            nama_pangkat: '',
            editId: null,
            editNama: '',
            listPangkat: @json($masterPangkat),

            async addPangkat() {
                if (!this.nama_pangkat) return;

                let res = await fetch('/kepegawaian/master_pangkat/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_pangkat: this.nama_pangkat
                    })
                });

                let data = await res.json();
                this.listPangkat.push(data);
                this.nama_pangkat = '';
            },

            startEdit(item) {
                this.editId = item.id;
                this.editNama = item.nama_pangkat;
            },

            cancelEdit() {
                this.editId = null;
                this.editNama = '';
            },

            async saveEdit(item) {
                if (!this.editNama) return;

                let res = await fetch(`/kepegawaian/master_pangkat/edit_master_pangkat/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_pangkat: this.editNama
                    })
                });

                if (res.ok) {
                    let data = await res.json();
                    let idx = this.listPangkat.findIndex(p => p.id === item.id);
                    if (idx !== -1) this.listPangkat[idx].nama_pangkat = data.nama_pangkat;
                    this.editId = null;
                    this.editNama = '';
                } else {
                    $dispatch('toast', { type: 'error', message: 'Gagal mengubah data' });
                }
            },

            async deletePangkat(id) {
                showConfirm({
                    type: 'danger',
                    title: 'Hapus Master Pangkat',
                    message: 'Data master pangkat yang dihapus tidak dapat dikembalikan. Yakin ingin menghapus?',
                    confirmText: 'Ya, Hapus',
                    callback: async () => {
                        let res = await fetch(
                        `/kepegawaian/master_pangkat/delete_master_pangkat/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (res.ok) {
                            this.listPangkat = this.listPangkat.filter(item => item.id !== id);
                        } else {
                            $dispatch('toast', { type: 'error', message: 'Gagal menghapus data' });
                        }
                    }
                });
            }
        }
    }

    function golonganComponent() {
        return {
            openGolongan: false,
            nama_golongan: '',
            editId: null,
            editNama: '',
            listGolongan: @json($masterGolongan),

            async addGolongan() {
                if (!this.nama_golongan) return;

                let res = await fetch('/kepegawaian/master_golongan/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_golongan: this.nama_golongan
                    })
                });

                let data = await res.json();
                this.listGolongan.push(data);
                this.nama_golongan = '';
            },

            startEdit(item) {
                this.editId = item.id;
                this.editNama = item.nama_golongan;
            },

            cancelEdit() {
                this.editId = null;
                this.editNama = '';
            },

            async saveEdit(item) {
                if (!this.editNama) return;

                let res = await fetch(`/kepegawaian/master_golongan/edit_master_golongan/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_golongan: this.editNama
                    })
                });

                if (res.ok) {
                    let data = await res.json();
                    let idx = this.listGolongan.findIndex(g => g.id === item.id);
                    if (idx !== -1) this.listGolongan[idx].nama_golongan = data.nama_golongan;
                    this.editId = null;
                    this.editNama = '';
                } else {
                    $dispatch('toast', { type: 'error', message: 'Gagal mengubah data' });
                }
            },

            async deleteGolongan(id) {
                showConfirm({
                    type: 'danger',
                    title: 'Hapus Master Golongan',
                    message: 'Data master golongan yang dihapus tidak dapat dikembalikan. Yakin ingin menghapus?',
                    confirmText: 'Ya, Hapus',
                    callback: async () => {
                        let res = await fetch(
                            `/kepegawaian/master_golongan/delete_master_golongan/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });

                        if (res.ok) {
                            this.listGolongan = this.listGolongan.filter(item => item.id !== id);
                        } else {
                            $dispatch('toast', { type: 'error', message: 'Gagal menghapus data' });
                        }
                    }
                });
            }
        }
    }
</script>
