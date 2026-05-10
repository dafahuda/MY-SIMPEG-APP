<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="sm:flex sm:justify-between sm:items-center mb-6">
            <div class="mb-4 sm:mb-0">
                <nav class="text-sm text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Backup</span>
                    <span>/</span>
                    <span>Database</span>
                </nav>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Backup <span class="text-base font-normal text-gray-500 dark:text-gray-400">Database</span>
                </h1>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-sm border border-gray-200 dark:border-gray-700 p-8">

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 text-center">
                Backup database berhasil. Download file kami hasil ...
            </p>

            <div class="flex justify-center">
                <a href="{{ route('backup_data.download') }}"
                    id="downloadBtn"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white text-sm font-medium rounded shadow-sm transition-colors duration-150">
                    {{-- Download icon --}}
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 12l-4.5-4.5 1.06-1.06L7 8.88V1h2v7.88l2.44-2.44L12.5 7.5 8 12zM2 14h12v-2H2v2z"/>
                    </svg>
                    Download
                </a>
            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        document.getElementById('downloadBtn').addEventListener('click', function () {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `
                <svg class="w-4 h-4 animate-spin fill-current" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1a7 7 0 1 0 7 7h-2a5 5 0 1 1-5-5V1z"/>
                </svg>
                Memproses...
            `;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.classList.remove('hover:bg-green-600');

            // Re-enable after a short delay to allow the download to start
            setTimeout(function () {
                btn.innerHTML = originalHtml;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.classList.add('hover:bg-green-600');
            }, 3000);
        });
    </script>
    @endpush
</x-app-layout>
