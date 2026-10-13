<div class="md:grid md:grid-cols-3 md:gap-6">
    <x-section-title>
        <x-slot name="title">{{ __('Update Password') }}</x-slot>
        <x-slot name="description">{{ __('Ensure your account is using a long, random password to stay secure.') }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form method="POST" action="{{ route('user-password.update') }}">
            @csrf
            @method('PUT')

            <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 shadow-sm sm:rounded-tl-md sm:rounded-tr-md">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="current_password" value="{{ __('Current Password') }}" />
                        <x-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                        <x-input-error for="current_password" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="password" value="{{ __('New Password') }}" />
                        <x-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error for="password" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                        <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error for="password_confirmation" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700/20 text-right sm:px-6 shadow-sm sm:rounded-bl-md sm:rounded-br-md">
                @if (session('status') === 'password-updated')
                    <div class="me-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Saved.') }}
                    </div>
                @endif

                <x-button>
                    {{ __('Save') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
