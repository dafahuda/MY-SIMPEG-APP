<div id="toast-container" class="simpeg-toast-container" aria-live="polite" aria-label="Notifikasi">
    @foreach (['success', 'error', 'warning', 'info'] as $type)
        @if ($message = session($type))
            <div class="simpeg-toast simpeg-toast-{{ $type }}" data-flash-type="{{ $type }}" role="{{ $type === 'error' ? 'alert' : 'status' }}">
                <span>{{ $message }}</span>
                <button type="button" aria-label="Tutup notifikasi">×</button>
            </div>
        @endif
    @endforeach
</div>
