<x-authentication-layout>
    <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Simpeg App') }}</h1>
    <p class="text-slate-400 font-semibold mb-4">Sistem Kepegawaian ASN berbasis website</p>
    <a href="{{ route('landing') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-6" data-testid="back-to-landing-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14" /><path d="m5 12 4 4" /><path d="m5 12 4-4" /></svg>
        Kembali ke halaman utama
    </a>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="username" value="{{ __('Username') }}" />
                <x-input id="username" type="text" name="username" :value="old('username')" required autofocus />
            </div>
            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" type="password" name="password" required autocomplete="current-password" />
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">
            <x-button>
                {{ __('Sign in') }}
            </x-button>
        </div>
    </form>
    <x-validation-errors class="mt-4" />
    <!-- Footer -->


    <footer class="bg-neutral-primary-soft rounded-base shadow-xs border border-default m-4">
        <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
            <span class="text-sm text-body sm:text-center">© 2026 <a href="https://flowbite.com/"
                    class="hover:underline">Simpeg™</a>. All Rights Reserved.
            </span>
        </div>
    </footer>

</x-authentication-layout>
