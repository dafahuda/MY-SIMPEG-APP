<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardeningTest extends TestCase
{
    public function test_timezone_adalah_wib(): void
    {
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
    }

    public function test_locale_indonesia_dan_pesan_validasi_terjemahan(): void
    {
        $this->assertSame('id', config('app.locale'));

        // pesan validasi bawaan harus bahasa Indonesia
        $pesan = __('validation.required', ['attribute' => 'nama']);
        $this->assertSame('Nama wajib diisi.', $pesan);
    }

    public function test_header_keamanan_ada_di_setiap_respons(): void
    {
        $res = $this->get('/login');

        $res->assertOk();
        $res->assertHeader('X-Frame-Options', 'DENY');
        $res->assertHeader('X-Content-Type-Options', 'nosniff');
        $res->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $res->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $this->assertStringContainsString("default-src 'self'", $res->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("frame-ancestors 'none'", $res->headers->get('Content-Security-Policy'));
    }

    public function test_log_channel_harian(): void
    {
        $this->assertSame('daily', config('logging.default'));
    }

    public function test_app_debug_mati(): void
    {
        // .env produksi/dev harus APP_DEBUG=false; nyalakan manual hanya saat debugging
        $this->assertFalse((bool) env('APP_DEBUG', false));
    }
}
