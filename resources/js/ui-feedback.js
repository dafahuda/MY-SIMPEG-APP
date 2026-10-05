// UI feedback uses text nodes only: user messages must never become HTML.
function notify(document, message, type = 'info', duration = 6000) {
    if (!['success', 'error', 'warning', 'info'].includes(type)) type = 'info';
    const container = document.getElementById('toast-container');
    if (!container) return;
    const notice = document.createElement('div');
    notice.className = `simpeg-toast simpeg-toast-${type}`;
    notice.setAttribute('role', type === 'error' ? 'alert' : 'status');
    const text = document.createElement('span');
    text.textContent = message;
    const close = document.createElement('button');
    close.type = 'button';
    close.textContent = '×';
    close.setAttribute('aria-label', 'Tutup notifikasi');
    notice.append(text, close);
    container.append(notice);
    manageNotice(document, notice, type, duration);
}

function manageNotice(document, notice, type, duration) {
    const dismiss = () => notice.remove();
    notice.querySelector('button')?.addEventListener('click', dismiss);
    // Errors and warnings stay visible until dismissed. Success/info timers pause on focus/hover.
    if (type === 'error' || type === 'warning') return;
    let timer;
    const pause = () => document.defaultView.clearTimeout(timer);
    const resume = () => { pause(); timer = document.defaultView.setTimeout(dismiss, duration); };
    notice.addEventListener('mouseenter', pause);
    notice.addEventListener('focusin', pause);
    notice.addEventListener('mouseleave', resume);
    notice.addEventListener('focusout', resume);
    resume();
}

function savePdf(document, blob, filename) {
    const url = URL.createObjectURL(blob);
    const download = document.createElement('a');
    download.href = url;
    download.download = filename;
    document.body.append(download);
    download.click();
    download.remove();
    setTimeout(() => URL.revokeObjectURL(url), 60000);
}

function applyFieldErrors(document) {
    const source = document.getElementById('validation-data');
    if (!source) return;
    let errors;
    try { errors = JSON.parse(source.textContent); } catch { return; }
    // Hide page-local summaries only when every list item is an actual validation message.
    // Leave domain warnings, instructions and non-error lists alone.
    const messagesSet = new Set(Object.values(errors).flat());
    document.querySelectorAll('main ul').forEach(list => {
        if (list.closest('.simpeg-validation-summary')) return;
        const items = [...list.querySelectorAll('li')];
        if (items.length && items.every(item => messagesSet.has(item.textContent.trim()))) {
            const banner = list.parentElement;
            if (banner.tagName === 'DIV' && /red-/.test(banner.className)) banner.hidden = true;
        }
    });
    Object.entries(errors).forEach(([name, messages]) => {
        const fields = [...document.querySelectorAll('main input, main select, main textarea')].filter(field => field.name === name || field.name === name.replace(/\.(\w+)/g, '[$1]'));
        fields.forEach((field, index) => {
            if (field.type === 'hidden' || field.dataset.errorLinked) return;
            const existing = field.nextElementSibling;
            const reuse = existing?.tagName === 'P' && existing.textContent.trim() === messages[0];
            const error = reuse ? existing : document.createElement('p');
            error.className = 'simpeg-field-error';
            error.dataset.fieldError = name;
            error.id = 'field-error-' + name.replace(/[^a-z0-9_-]/gi, '-') + '-' + index;
            error.textContent = messages[0];
            if (!reuse) field.insertAdjacentElement('afterend', error);
            field.setAttribute('aria-invalid', 'true');
            field.setAttribute('aria-describedby', [field.getAttribute('aria-describedby'), error.id].filter(Boolean).join(' '));
            field.dataset.errorLinked = 'true';
        });
    });
    document.querySelector('main [aria-invalid="true"]')?.focus({ preventScroll: true });
}

function enhanceTables(document) {
    document.querySelectorAll('main table:not(.print-area table):not(.print-table)').forEach((table, index) => {
        if (table.dataset.responsiveReady) return;
        const headerRows = [...(table.tHead?.rows || [])];
        const headers = [...(headerRows.at(-1)?.cells || [])];
        if (!headers.length) return;
        table.dataset.responsiveReady = 'true';
        headerRows.forEach(row => [...row.cells].forEach(header => header.setAttribute('scope', header.colSpan > 1 ? 'colgroup' : 'col')));
        const simple = headerRows.length === 1 && headers.every(h => h.colSpan === 1 && h.rowSpan === 1);
        if (simple) {
            [...table.tBodies].forEach(body => [...body.rows].forEach(row => {
                if (row.cells.length !== headers.length || [...row.cells].some(cell => cell.colSpan > 1 || cell.rowSpan > 1)) return;
                [...row.cells].forEach((cell, i) => { cell.dataset.label = headers[i].textContent.trim(); });
            }));
            if (headers.length <= 6) table.classList.add('simpeg-table-cards');
        }
        table.classList.add('simpeg-table');
        const region = document.createElement('div');
        region.className = 'simpeg-table-scroll';
        region.tabIndex = 0;
        region.setAttribute('role', 'region');
        region.setAttribute('aria-label', 'Tabel ' + (document.querySelector('main h1')?.textContent.trim() || (index + 1)));
        table.before(region);
        region.append(table);
        const hint = document.createElement('p');
        hint.className = 'simpeg-table-hint';
        hint.textContent = headers.length <= 6 && simple ? 'Daftar ditampilkan per baris pada layar kecil.' : 'Geser tabel ke samping untuk melihat semua kolom.';
        region.before(hint);
    });
}

export function installUiFeedback(document, options = {}) {
    if (document.documentElement.dataset.uiFeedback) return;
    document.documentElement.dataset.uiFeedback = 'true';
    const window = document.defaultView;
    const duration = options.toastDuration ?? 6000;
    window.toast = (message, type = 'info') => notify(document, String(message), type, duration);
    window.addEventListener('toast', event => {
        if (event.detail?.message) window.toast(event.detail.message, event.detail.type);
    });
    document.querySelectorAll('[data-flash-type]').forEach(notice => manageNotice(document, notice, notice.dataset.flashType, duration));
    applyFieldErrors(document);
    enhanceTables(document);
    document.addEventListener('livewire:navigated', () => { applyFieldErrors(document); enhanceTables(document); });
    const fetchPdf = options.fetch || window.fetch.bind(window);
    const save = options.save || ((blob, name) => savePdf(document, blob, name));
    document.addEventListener('click', async event => {
        const link = event.target.closest('a[href]');
        if (!link || !link.closest('main') || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.hasAttribute('download')) return;
        const url = new URL(link.href, document.location.href);
        if (url.origin !== document.location.origin || !/(?:\/pdf\/|\/unduh_pdf(?:\/|$)|\/biodata_pdf\/)/.test(url.pathname)) return;
        event.preventDefault();
        if (link.getAttribute('aria-busy') === 'true') return;
        const original = [...link.childNodes];
        link.setAttribute('aria-busy', 'true');
        link.setAttribute('aria-disabled', 'true');
        const spinner = document.createElement('span');
        spinner.className = 'simpeg-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        link.replaceChildren(spinner, document.createTextNode(' Menyiapkan PDF…'));
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 90000);
        try {
            const response = await fetchPdf(url.href, { credentials: 'same-origin', headers: { Accept: 'application/pdf' }, signal: controller.signal });
            if (!response.ok || !response.headers.get('Content-Type')?.includes('application/pdf')) throw new Error('invalid-pdf');
            const blob = await response.blob();
            if (!(await blob.slice(0, 5).text()).startsWith('%PDF')) throw new Error('invalid-pdf');
            const disposition = response.headers.get('Content-Disposition') || '';
            const filename = disposition.match(/filename="([^"]+)"/)?.[1] || 'laporan.pdf';
            save(blob, filename);
            notify(document, 'PDF siap diunduh.', 'success');
        } catch (error) {
            notify(document, error.name === 'AbortError' ? 'Pembuatan PDF terlalu lama. Silakan coba lagi.' : 'Gagal mengunduh PDF. Periksa sesi login dan filter laporan, lalu coba lagi.', 'error');
        } finally {
            clearTimeout(timer);
            link.replaceChildren(...original);
            link.removeAttribute('aria-busy');
            link.removeAttribute('aria-disabled');
        }
    });
}
