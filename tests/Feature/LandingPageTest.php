<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_dapat_membuka_landing_dengan_tautan_masuk(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('landing')
            ->assertSee('lang="id"', false)
            ->assertSee('Sistem Informasi Kepegawaian')
            ->assertSee('href="'.route('login').'"', false)
            ->assertSee('Masuk')
            ->assertDontSee('href="'.route('dashboard').'"', false);
    }

    public function test_pengguna_login_mendapat_tautan_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertViewIs('landing')
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertSee('Dashboard')
            ->assertDontSee('href="'.route('login').'"', false);
    }

    public function test_landing_tidak_menampilkan_identitas_pribadi(): void
    {
        $user = User::factory()->create(['name' => 'Nama Akun Rahasia']);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $user->id,
            'nama' => 'Nama Pegawai Rahasia',
            'nip' => '198501012010011999',
        ]);

        foreach ([false, true] as $authenticated) {
            if ($authenticated) {
                $this->actingAs($user);
            }

            $this->get('/')
                ->assertOk()
                ->assertDontSee($user->name)
                ->assertDontSee($user->username)
                ->assertDontSee($user->email)
                ->assertDontSee($pegawai->nama)
                ->assertDontSee($pegawai->nip);
        }
    }

    public function test_landing_memakai_aset_ringan_dan_navigasi_aksesibel(): void
    {
        $response = $this->get('/')->assertOk();
        $response->assertSee('landing-', false)
            ->assertDontSee('build/assets/app-', false)
            ->assertDontSee('livewire', false)
            ->assertSee('href="#konten-utama"', false)
            ->assertSee('id="konten-utama"', false)
            ->assertSee('aria-label="Navigasi utama"', false)
            ->assertSee('data-theme-toggle', false);
    }

    public function test_landing_lingkungan_demo_menampilkan_akun_demo(): void
    {
        config(['app.env' => 'staging']);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-testid="demo-accounts"', false)
            ->assertSee('superadmin.demo')
            ->assertSee('admin.bkpsdm')
            ->assertSee('admin.dinkes')
            ->assertSee('pegawai.andi')
            ->assertSee('pegawai.bela')
            ->assertSee('pegawai.citra')
            ->assertSee('demo123')
            ->assertSee('Hanya tersedia di lingkungan demo');
    }

    public function test_landing_lingkungan_produksi_menyembunyikan_akun_demo(): void
    {
        // APP_ENV='testing' saat suite berjalan; paksa environment produksi hanya untuk render view ini.
        $app = app();
        $app->detectEnvironment(fn () => 'production');
        try {
            $rendered = view('landing')->render();
        } finally {
            $app->detectEnvironment(fn () => 'testing');
        }

        $this->assertStringNotContainsString('demo123', $rendered);
        $this->assertStringNotContainsString('superadmin.demo', $rendered);
    }

    public function test_halaman_login_tetap_dapat_dibuka_tamu(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('href="'.route('landing').'"', false)
            ->assertSee('data-testid="back-to-landing-link"', false)
            ->assertSee('Kembali ke halaman utama');
    }

    public function test_rute_internal_tetap_memerlukan_login(): void
    {
        foreach (['/dashboard', '/profile_saya', '/data_pegawai/pegawai', '/report/nominatif', '/backup_data'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }
}
