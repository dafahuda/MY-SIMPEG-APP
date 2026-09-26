<?php

namespace Tests\Feature;

use App\Models\KGB;
use App\Models\Pegawai;
use App\Models\User;
use App\Notifications\KgbJatuhTempoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class KirimReminderKgbTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_kirim_notifikasi_untuk_kgb_jatuh_tempo(): void
    {
        Notification::fake();

        $unit = \App\Models\UnitKerja::factory()->create();
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        [$pegawaiUser, $pegawai] = $this->makePegawai($unit->id);

        // KGB jatuh tempo 30 hari ke depan → harus terdeteksi
        KGB::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tmt_kgb' => now()->addDays(30)->toDateString(),
        ]);

        // KGB sudah lewat → tidak boleh terdeteksi
        KGB::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tmt_kgb' => now()->subDays(10)->toDateString(),
        ]);

        // KGB terlalu jauh (lebih dari 60 hari) → tidak boleh terdeteksi
        KGB::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tmt_kgb' => now()->addDays(120)->toDateString(),
        ]);

        $this->artisan('kgb:reminder')
            ->expectsOutputToContain('1 KGB jatuh tempo')
            ->assertSuccessful();

        // Notifikasi masuk ke superadmin dan admin unit terkait
        Notification::assertSentTo($superadmin, KgbJatuhTempoNotification::class, 1);
        Notification::assertSentTo($admin, KgbJatuhTempoNotification::class, 1);
        // Pegawai biasa tidak menerima notifikasi KGB
        Notification::assertNotSentTo($pegawaiUser, KgbJatuhTempoNotification::class);
    }

    public function test_command_tanpa_kgb_jatuh_tempo(): void
    {
        Notification::fake();

        $this->artisan('kgb:reminder')
            ->expectsOutputToContain('Tidak ada KGB')
            ->assertSuccessful();

        Notification::assertNothingSent();
    }

    private function makePegawai(int $unitId): array
    {
        $user = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitId]);
        $pegawai = Pegawai::factory()->create(['user_id' => $user->id, 'unit_kerja_id' => $unitId]);

        return [$user, $pegawai];
    }
}
