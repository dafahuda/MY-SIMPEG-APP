<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Simpeg App') }}</title>

        <link rel="icon" type="image/png" href="images/logo_asn.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <script>
            if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
                document.querySelector('html').classList.remove('dark');
                document.querySelector('html').style.colorScheme = 'light';
            } else {
                document.querySelector('html').classList.add('dark');
                document.querySelector('html').style.colorScheme = 'dark';
            }
        </script>
    </head>

    <body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
        :class="{ 'sidebar-expanded': sidebarExpanded }" x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }" x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))">

        <script>
            if (localStorage.getItem('sidebar-expanded') == 'true') {
                document.querySelector('body').classList.add('sidebar-expanded');
            } else {
                document.querySelector('body').classList.remove('sidebar-expanded');
            }
        </script>

        <!-- Page wrapper -->
        <div class="flex h-[100dvh] overflow-hidden">

            <x-app.sidebar :variant="$attributes['sidebarVariant']" />

            <!-- Content area -->
            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if ($attributes['background']) {{ $attributes['background'] }} @endif"
                x-ref="contentarea">

                <x-app.header :variant="$attributes['headerVariant']" />

                <main class="grow">
                    <div class="px-4 sm:px-6 lg:px-8 mt-4">
                        @include('components.flash-message')
                    </div>
                    {{ $slot }}
                </main>

            </div>

        </div>

        {{-- Modal Konfirmasi Global --}}
        <x-app.confirm-modal />

        {{-- Event delegation untuk tombol Edit & Delete dengan konfirmasi --}}
        <script>
        document.addEventListener('click', function(e) {
            // Tombol Edit dengan konfirmasi
            const editLink = e.target.closest('.confirm-edit');
            if (editLink) {
                e.preventDefault();
                const href    = editLink.getAttribute('href');
                const title   = editLink.dataset.title   || 'Edit Data';
                const message = editLink.dataset.message || 'Anda akan membuka form edit data ini. Lanjutkan?';
                showConfirm({
                    type: 'warning',
                    title: title,
                    message: message,
                    confirmText: 'Ya, Edit',
                    callback: () => { window.location.href = href; }
                });
                return;
            }

            // Tombol Delete dengan konfirmasi
            const deleteBtn = e.target.closest('.confirm-delete');
            if (deleteBtn) {
                e.preventDefault();
                const form    = deleteBtn.closest('form');
                const title   = deleteBtn.dataset.title   || 'Hapus Data';
                const message = deleteBtn.dataset.message || 'Data yang dihapus tidak dapat dikembalikan. Yakin ingin menghapus?';
                showConfirm({
                    type: 'danger',
                    title: title,
                    message: message,
                    confirmText: 'Ya, Hapus',
                    callback: () => { form.submit(); }
                });
                return;
            }

            // Tombol Save/Update dengan konfirmasi
            const saveBtn = e.target.closest('.confirm-save');
            if (saveBtn) {
                e.preventDefault();
                const form    = saveBtn.closest('form');
                const title   = saveBtn.dataset.title   || 'Simpan Perubahan';
                const message = saveBtn.dataset.message || 'Apakah Anda yakin ingin menyimpan perubahan data ini?';
                showConfirm({
                    type: 'info',
                    title: title,
                    message: message,
                    confirmText: 'Ya, Simpan',
                    callback: () => { form.submit(); }
                });
                return;
            }
        });
        </script>

        @livewireScriptConfig
        @stack('scripts')
    </body>

</html>
