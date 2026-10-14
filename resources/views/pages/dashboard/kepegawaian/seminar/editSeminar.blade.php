<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard actions -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">

            <!-- Left: Title -->
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Form edit seminar</h1>
            </div>

        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-5">
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
                <form action="/kepegawaian/seminar/edit_seminar/{{ $seminar->id }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('pages.dashboard.kepegawaian.seminar._fields', ['piagamDateLabel' => 'Tanggal'])

                    <div class="mb-5">
                        <label for="file_piagam" class="block mb-2.5 text-sm font-medium text-heading">File
                            Piagam <span class="text-red-500">*</span></label>
                        <input type="file" id="file_piagam" name="file_piagam"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            placeholder="Masukan file piagam" />
                    </div>
                    <button type="button"
                        class="confirm-save text-white bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 rounded-lg focus:ring-blue-500 shadow-lg font-medium leading-5 text-sm px-4 py-2 focus:outline-none"
                        data-title="Konfirmasi Simpan"
                        data-message="Apakah Anda yakin ingin menyimpan perubahan data seminar ini?">Simpan</button>
                </form>
            </div>

            <div class="mt-5">
                <a href="/kepegawaian/seminar"
                    class="inline-block rounded-lg shadow-lg text-white px-4 py-2 bg-slate-500 hover:bg-slate-700">Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
