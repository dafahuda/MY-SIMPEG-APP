@props(['status'])

@php
    $normalizedStatus = in_array($status, ['draft', 'planned', 'realized', 'cancelled'], true) ? $status : 'draft';
    $label = \App\Support\DiklatGlossary::statusLabel($normalizedStatus);

    $classes = match ($normalizedStatus) {
        'planned' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300',
        'realized' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-300',
        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-300',
        default => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-300',
    };
@endphp

<span data-testid="rencana-diklat-status-badge" data-status="{{ $normalizedStatus }}"
    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $classes }}">
    {{ $label }}
</span>
