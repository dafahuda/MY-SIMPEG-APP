{{-- Modal Konfirmasi Global — dipanggil via showConfirm({...}) dari mana saja --}}
<div
    x-data="confirmModalState()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @open-confirm.window="show($event.detail)"
    @keydown.escape.window="cancel()"
>
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="cancel()"></div>

    {{-- Modal Box --}}
    <div
        class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm mx-4 p-6"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
    >
        {{-- Icon --}}
        <div class="flex items-center justify-center w-12 h-12 rounded-full mx-auto mb-4"
            :class="{
                'bg-red-100 dark:bg-red-900/30'    : type === 'danger',
                'bg-yellow-100 dark:bg-yellow-900/30': type === 'warning',
                'bg-blue-100 dark:bg-blue-900/30'  : type === 'info'
            }">
            <template x-if="type === 'danger'">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </template>
            <template x-if="type === 'warning'">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </template>
            <template x-if="type === 'info'">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
        </div>

        {{-- Title --}}
        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 text-center mb-2" x-text="title"></h3>

        {{-- Message --}}
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6" x-text="message"></p>

        {{-- Buttons --}}
        <div class="flex gap-3">
            <button @click="cancel()"
                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">
                Batal
            </button>
            <button @click="confirm()"
                class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-lg transition"
                :class="{
                    'bg-red-500 hover:bg-red-600'      : type === 'danger',
                    'bg-yellow-500 hover:bg-yellow-600': type === 'warning',
                    'bg-blue-500 hover:bg-blue-600'    : type === 'info'
                }"
                x-text="confirmText">
            </button>
        </div>
    </div>
</div>

<script>
function confirmModalState() {
    return {
        open: false,
        type: 'danger',
        title: '',
        message: '',
        confirmText: 'Ya, Lanjutkan',
        _callback: null,

        show(detail) {
            this.type        = detail.type        || 'danger';
            this.title       = detail.title       || 'Konfirmasi';
            this.message     = detail.message     || 'Apakah Anda yakin?';
            this.confirmText = detail.confirmText || 'Ya, Lanjutkan';
            this._callback   = detail.callback    || null;
            this.open = true;
        },

        confirm() {
            this.open = false;
            if (typeof this._callback === 'function') this._callback();
        },

        cancel() {
            this.open = false;
            this._callback = null;
        }
    }
}

// Helper global — panggil dari atribut onclick / @click di mana saja
function showConfirm(options) {
    window.dispatchEvent(new CustomEvent('open-confirm', { detail: options }));
}
</script>
