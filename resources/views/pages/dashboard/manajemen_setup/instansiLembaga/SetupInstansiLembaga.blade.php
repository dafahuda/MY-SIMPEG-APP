<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Setup Instansi Lembaga</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-5">
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
                <form action="/manajemen_setup/setupInstansiLembaga/{{ $instansiLembaga->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="gambarLama" id="gambarLama">
                    <div class="mb-5">
                        <label for="nama_instansi_lembaga" class="block mb-2.5 text-sm font-medium text-heading">Nama
                            Instansi Lembaga</label>
                        <input type="text" id="nama_instansi_lembaga" name="nama_instansi_lembaga"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nama instansi lembaga" required
                            value="{{ old('nama_instansi_lembaga', $instansiLembaga->nama_instansi_lembaga) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="kabupaten_kota" class="block mb-2.5 text-sm font-medium text-heading">Kabupaten /
                            Kota</label>
                        <select name="kabupaten_kota" id="kabupaten_kota">
                            <option value="Kabupaten"
                                {{ old('kabupaten_kota', $instansiLembaga->kabupaten_kota) == 'Kabupaten' ? 'selected' : '' }}>
                                Kabupaten
                            </option>
                            <option value="Kota"
                                {{ old('kabupaten_kota', $instansiLembaga->kabupaten_kota) == 'Kota' ? 'selected' : '' }}>
                                Kota
                            </option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="alamat" class="block mb-2.5 text-sm font-medium text-heading">Alamat</label>

                        <textarea id="alamat" name="alamat" rows="4" cols="50">{{ old('alamat', $instansiLembaga->alamat) }}</textarea>
                    </div>
                    <div class="mb-5">
                        <label for="no_telp" class="block mb-2.5 text-sm font-medium text-heading">Nomor
                            Telepon <span class="text-red-500">*</span></label>
                        <input type="text" id="no_telp" name="no_telp"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan nomor telepon" required
                            value="{{ old('no_telp', $instansiLembaga->no_telp) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="email" class="block mb-2.5 text-sm font-medium text-heading">Pejabat Pemulihan
                            Hukuman <span class="text-red-500">*</span> </label>
                        <input type="text" id="email" name="email"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan email instansi lembaga" required
                            value="{{ old('email', $instansiLembaga->email) }}" />
                    </div>
                    <div class="mb-5">
                        <label for="kepala_dinas" class="block mb-2.5 text-sm font-medium text-heading">Kepala
                            Dinas <span class="text-red-500">*</span> </label>
                        <input type="text" id="kepala_dinas" name="kepala_dinas"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan kepala dinas" required
                            value="{{ old('kepala_dinas', $instansiLembaga->kepala_dinas) }}" />
                    </div>
                    <div class="mb-5">
                        @if ($instansiLembaga->gambar_logo)
                            <img src="{{ asset($instansiLembaga->gambar_logo) }}" alt="" width="300px"
                                height="200px">
                        @else
                            <img alt="" class="img-preview img-fluid mb-3 col-sm-5">
                        @endif
                        <div class="img-preview">
                            <label for="gambar_logo" class="block mb-2.5 text-sm font-medium text-heading">Logo
                                Instansi <span class="text-red-500">*</span> </label>
                            <input type="file" id="gambar_logo" name="gambar_logo"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder="Masukan logo instansi" />
                        </div>
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-brand-medium shadow-lg font-medium leading-5 rounded-base text-sm px-4 py-2 focus:outline-none">Save</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/manajemen_setup/instansi_lembaga"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
<script text="text/javascript">
    function previewImage() {
        const foto = document.querySelector('#foto');
        const imgPreview = document.querySelector('.img-preview');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(foto.files[0]);

        oFReader.onload = function(oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }
</script>
