import { test } from 'node:test';
import assert from 'node:assert/strict';
import { createRequire } from 'node:module';
import { pathToFileURL } from 'node:url';
const require = createRequire(import.meta.url);
const { JSDOM } = require(process.env.SIMPEG_JSDOM || 'jsdom');
const moduleUrl = pathToFileURL(process.cwd() + '/resources/js/ui-feedback.js');

function dom(html) {
    return new JSDOM(html, { url: 'https://simpeg.test', pretendToBeVisual: true });
}

test('PDF shows busy state until real response and restores link on success', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><a href="/report/pdf/nominatif">Unduh PDF</a></main><div id="toast-container"></div>');
    let resolve;
    const pending = new Promise(r => { resolve = r; });
    let calls = 0;
    let saved;
    installUiFeedback(page.window.document, { fetch: () => { calls++; return pending; }, save: (blob, filename) => { saved = { blob, filename }; } });
    const link = page.window.document.querySelector('a');
    link.click();
    link.click();
    assert.equal(link.getAttribute('aria-busy'), 'true');
    assert.match(link.textContent, /Menyiapkan PDF/);
    assert.equal(calls, 1);
    resolve(new Response('%PDF-1.4 sample', { headers: { 'Content-Type': 'application/pdf', 'Content-Disposition': 'attachment; filename="laporan.pdf"' } }));
    await new Promise(r => setTimeout(r, 20));
    assert.equal(saved.filename, 'laporan.pdf');
    assert.equal(saved.blob.type, 'application/pdf');
    assert.equal(link.textContent, 'Unduh PDF');
    assert.equal(link.hasAttribute('aria-busy'), false);
    page.window.close();
});

test('session and JS toast use one container, safe text, dismiss and persistent errors', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main></main><div id="toast-container"><div data-flash-type="warning">Perhatian<button type="button" aria-label="Tutup notifikasi">×</button></div></div>');
    installUiFeedback(page.window.document, { fetch: async () => {}, toastDuration: 10 });
    page.window.dispatchEvent(new page.window.CustomEvent('toast', { detail: { type: 'error', message: '<img src=x onerror=alert(1)>' } }));
    page.window.toast('Berhasil', 'success');
    assert.equal(page.window.document.querySelectorAll('#toast-container').length, 1);
    assert.equal(page.window.document.querySelector('#toast-container img'), null);
    assert.match(page.window.document.querySelector('#toast-container').textContent, /<img src=x/);
    await new Promise(r => setTimeout(r, 30));
    assert.equal(page.window.document.querySelectorAll('.simpeg-toast-success').length, 0);
    assert.equal(page.window.document.querySelectorAll('.simpeg-toast-error').length, 1);
    page.window.document.querySelector('.simpeg-toast-error button').click();
    assert.equal(page.window.document.querySelectorAll('.simpeg-toast-error').length, 0);
    page.window.close();
});

test('field errors mark inputs, preserve values and do not duplicate messages', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><form><label for="nama">Nama</label><input id="nama" name="nama" value="Isi lama"><textarea name="alamat"></textarea></form></main><script type="application/json" id="validation-data">{"nama":["Nama wajib diisi."],"alamat":["Alamat wajib diisi."]}</script><div id="toast-container"></div>');
    installUiFeedback(page.window.document, { fetch: async () => {} });
    const input = page.window.document.querySelector('input');
    assert.equal(input.getAttribute('aria-invalid'), 'true');
    assert.equal(input.value, 'Isi lama');
    const errorId = input.getAttribute('aria-describedby');
    assert.equal(page.window.document.getElementById(errorId).textContent, 'Nama wajib diisi.');
    assert.equal(page.window.document.querySelector('textarea').getAttribute('aria-invalid'), 'true');
    installUiFeedback(page.window.document, { fetch: async () => {} });
    assert.equal(page.window.document.querySelectorAll('[data-field-error]').length, 2);
    page.window.close();
});

test('mobile tables get headers, local scroll region and keyboard accessibility without duplicate wrappers', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><h1>Daftar pegawai</h1><table><thead><tr><th>NIP</th><th>Nama</th><th>Aksi</th></tr></thead><tbody><tr><td>123</td><td>Andi</td><td><button>Edit</button></td></tr><tr><td colspan="3">Kosong</td></tr></tbody></table></main><div id="toast-container"></div>');
    installUiFeedback(page.window.document, { fetch: async () => {} });
    const cells = page.window.document.querySelectorAll('tbody tr:first-child td');
    assert.equal(cells[0].dataset.label, 'NIP');
    assert.equal(cells[2].dataset.label, 'Aksi');
    assert.equal(page.window.document.querySelector('.simpeg-table-scroll').getAttribute('tabindex'), '0');
    assert.equal(page.window.document.querySelector('th').getAttribute('scope'), 'col');
    assert.equal(page.window.document.querySelector('td[colspan]').hasAttribute('data-label'), false);
    installUiFeedback(page.window.document, { fetch: async () => {} });
    assert.equal(page.window.document.querySelectorAll('.simpeg-table-scroll').length, 1);
    page.window.close();
});

test('network failure unlocks PDF and gives retry feedback', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><a href="/rekapitulasi/pdf/golongan">Unduh PDF</a></main><div id="toast-container"></div>');
    installUiFeedback(page.window.document, { fetch: async () => { throw new Error('offline'); } });
    page.window.document.querySelector('a').click();
    await new Promise(r => setTimeout(r, 20));
    assert.equal(page.window.document.querySelector('a').hasAttribute('aria-busy'), false);
    assert.match(page.window.document.querySelector('#toast-container').textContent, /coba lagi/);
    page.window.close();
});

test('existing inline field errors are linked instead of repeated', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><form><input name="nama"><p class="text-red-600">Nama wajib diisi.</p></form></main><script type="application/json" id="validation-data">{"nama":["Nama wajib diisi."]}</script><div id="toast-container"></div>');
    installUiFeedback(page.window.document, { fetch: async () => {} });
    assert.equal(page.window.document.querySelectorAll('form p').length, 1);
    const input = page.window.document.querySelector('input');
    assert.equal(input.getAttribute('aria-invalid'), 'true');
    assert.equal(page.window.document.getElementById(input.getAttribute('aria-describedby')).textContent, 'Nama wajib diisi.');
    page.window.close();
});

test('grouped tables retain native table layout and header scopes', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><table><thead><tr><th rowspan="2">Unit</th><th colspan="2">Jumlah</th></tr><tr><th>Laki-laki</th><th>Perempuan</th></tr></thead><tbody><tr><td>Unit A</td><td>1</td><td>2</td></tr></tbody></table></main><div id="toast-container"></div>');
    installUiFeedback(page.window.document, { fetch: async () => {} });
    assert.equal(page.window.document.querySelector('table').classList.contains('simpeg-table-cards'), false);
    assert.equal(page.window.document.querySelector('th[colspan]').getAttribute('scope'), 'colgroup');
    page.window.close();
});

test('print-only tables are not converted or wrapped', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><div class="print-area"><table><thead><tr><th>Nama</th></tr></thead><tbody><tr><td>Andi</td></tr></tbody></table></div></main><div id="toast-container"></div>');
    installUiFeedback(page.window.document, { fetch: async () => {} });
    assert.equal(page.window.document.querySelectorAll('.simpeg-table-scroll').length, 0);
    page.window.close();
});

test('only one validation summary remains visible if page already has summary', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><div class="simpeg-validation-summary"><ul><li>Nama wajib diisi.</li></ul></div><form><div class="bg-red-500"><ul><li>Nama wajib diisi.</li></ul></div><input name="nama"></form></main><script id="validation-data" type="application/json">{"nama":["Nama wajib diisi."]}</script><div id="toast-container"></div>');
    installUiFeedback(page.window.document, { fetch: async () => {} });
    assert.equal(page.window.document.querySelector('.bg-red-500').hidden, true);
    assert.equal(page.window.document.querySelector('.simpeg-validation-summary').hidden, false);
    page.window.close();
});

for (const [name, status, body] of [['server error', 500, '%PDF-error'], ['invalid signature', 200, 'not a PDF']]) {
    test(`PDF rejects ${name} and restores the link`, async () => {
        const { installUiFeedback } = await import(moduleUrl);
        const page = dom('<main><a href="/report/pdf/nominatif">Unduh PDF</a></main><div id="toast-container"></div>');
        let saved = false;
        installUiFeedback(page.window.document, { fetch: async () => new Response(body, { status, headers: { 'Content-Type': 'application/pdf' } }), save: () => { saved = true; } });
        const link = page.window.document.querySelector('a');
        link.click();
        await new Promise(r => setTimeout(r, 20));
        assert.equal(saved, false);
        assert.equal(link.textContent, 'Unduh PDF');
        assert.equal(link.hasAttribute('aria-busy'), false);
        assert.match(page.window.document.querySelector('#toast-container').textContent, /Gagal mengunduh PDF/);
        page.window.close();
    });
}

test('PDF timeout aborts request and restores controls', async context => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><a href="/report/pdf/nominatif">Unduh PDF</a></main><div id="toast-container"></div>');
    context.mock.timers.enable({ apis: ['setTimeout'] });
    let signal;
    installUiFeedback(page.window.document, { fetch: (_url, options) => new Promise((_resolve, reject) => {
        signal = options.signal;
        signal.addEventListener('abort', () => reject(new DOMException('Timeout', 'AbortError')));
    }) });
    const link = page.window.document.querySelector('a');
    link.click();
    context.mock.timers.tick(90000);
    await new Promise(r => setImmediate(r));
    assert.equal(signal.aborted, true);
    assert.equal(link.hasAttribute('aria-busy'), false);
    assert.match(page.window.document.querySelector('#toast-container').textContent, /terlalu lama/);
    page.window.close();
});

test('PDF rejects HTML redirects and restores controls with visible error', async () => {
    const { installUiFeedback } = await import(moduleUrl);
    const page = dom('<main><a href="/profile_saya/unduh_pdf">Unduh PDF</a></main><div id="toast-container"></div>');
    let saved = false;
    installUiFeedback(page.window.document, { fetch: async () => new Response('<html>Login</html>', { headers: { 'Content-Type': 'text/html' } }), save: () => { saved = true; } });
    const link = page.window.document.querySelector('a');
    link.click();
    await new Promise(r => setTimeout(r, 20));
    assert.equal(saved, false);
    assert.equal(link.hasAttribute('aria-busy'), false);
    assert.match(page.window.document.querySelector('#toast-container').textContent, /Gagal mengunduh PDF/);
    page.window.close();
});
