<?php

namespace Tests\Feature;

use App\Models\Kgb as KGBAlias;
use App\Models\Pegawai;
use App\Models\User;
use App\Notifications\KgbJatuhTempoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotifikasiHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_badge_menampilkan_jumlah_belum_dibaca(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $unit = \App\Models\UnitKerja::factory()->create();
        [$pegawaiUser, $pegawai] = $this->makePegawai($unit->id);

        // Sebelum ada notifikasi: dropdown menampilkan empty state
        $res = $this->actingAs($superadmin)->get('/dashboard');
        $res->assertOk();
        $this->assertEquals(1, substr_count($res->getContent(), 'Belum ada notifikasi'));

        // Kirim 2 notifikasi KGB
        $kgb = \App\Models\KGB::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tmt_kgb' => now()->addDays(30)->toDateString(),
        ]);
        $superadmin->notify(new KgbJatuhTempoNotification($kgb, 60));
        $superadmin->notify(new KgbJatuhTempoNotification($kgb, 30));

        $res = $this->actingAs($superadmin)->get('/dashboard');
        $res->assertOk();

        // Badge jumlah = 2 dan isi notifikasi tampil
        $this->assertMatchesRegularExpression('/>\s*2\s*</', $res->getContent());
        $this->assertStringContainsString('KGB Jatuh Tempo', $res->getContent());
        $this->assertStringContainsString($pegawai->nama, $res->getContent());
    }

    public function test_tandai_semua_dibaca_mengosongkan_badge(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $unit = \App\Models\UnitKerja::factory()->create();
        [$pegawaiUser, $pegawai] = $this->makePegawai($unit->id);

        $kgb = \App\Models\KGB::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tmt_kgb' => now()->addDays(45)->toDateString(),
        ]);
        $superadmin->notify(new KgbJatuhTempoNotification($kgb, 60));

        $this->assertSame(1, $superadmin->unreadNotifications()->count());

        $res = $this->actingAs($superadmin)->post('/notifications/read-all');
        $res->assertRedirect();

        $this->assertSame(0, $superadmin->fresh()->unreadNotifications()->count());
    }

    public function test_route_notifikasi_butuh_login(): void
    {
        $this->post('/notifications/read-all')->assertRedirect('/login');
    }

    private function makePegawai(int $unitId): array
    {
        $user = User::factory()->create(['role' => 'pegawai']);
        $pegawai = Pegawai::factory()->create(['user_id' => $user->id, 'unit_kerja_id' => $unitId]);

        return [$user, $pegawai];
    }
}
