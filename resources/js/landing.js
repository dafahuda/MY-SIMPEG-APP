const toggle = document.querySelector('[data-theme-toggle]');
const root = document.documentElement;

function setTheme(dark) {
    root.classList.toggle('dark', dark);
    root.style.colorScheme = dark ? 'dark' : 'light';
    toggle?.setAttribute('aria-pressed', String(dark));
    toggle?.setAttribute('aria-label', dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
    if (toggle) toggle.textContent = dark ? 'Mode terang' : 'Mode gelap';
    try { localStorage.setItem('dark-mode', String(dark)); } catch { /* Theme still works when browser storage is unavailable. */ }
}

setTheme(root.classList.contains('dark'));
toggle?.addEventListener('click', () => setTheme(!root.classList.contains('dark')));
