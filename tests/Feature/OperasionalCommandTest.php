<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OperasionalCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_database_membuat_file_dan_retensi(): void
    {
        Storage::fake('backup');

        // backup lama (40 hari) harus terhapus, baru tidak
        Storage::disk('backup')->put('backup_lama_2026-08-01.sql', '-- dummy lama');
        Storage::disk('backup')->assertExists('backup_lama_2026-08-01.sql');

        // set lastModified file lama ke 40 hari lalu
        $path = Storage::disk('backup')->path('backup_lama_2026-08-01.sql');
        touch($path, now()->subDays(40)->getTimestamp());

        $this->artisan('backup:database', ['--days' => 30])
            ->expectsOutputToContain('Backup tersimpan')
            ->assertSuccessful();

        $files = Storage::disk('backup')->files();
        $this->assertCount(1, $files, 'Backup baru harus tersimpan, backup lama (>30 hari) harus terhapus');
        $this->assertMatchesRegularExpression('/^backup_.*\.sql$/', basename($files[0]));

        // isi file berisi header dump
        $isi = Storage::disk('backup')->get($files[0]);
        $this->assertStringContainsString('-- Database Backup:', $isi);
        $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS=0;', $isi);
    }

    public function test_notifikasi_prune_menghapus_yang_lama(): void
    {
        // notifikasi lama (100 hari) & baru (5 hari)
        $tanggalLama = now()->subDays(100);
        DB::table('notifications')->insert([
            'id' => 'old-1',
            'type' => 'App\\Notifications\\KgbReminder',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => '{"judul":"KGB lama"}',
            'read_at' => null,
            'created_at' => $tanggalLama,
            'updated_at' => $tanggalLama,
        ]);
        DB::table('notifications')->insert([
            'id' => 'new-1',
            'type' => 'App\\Notifications\\KgbReminder',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => 1,
            'data' => '{"judul":"KGB baru"}',
            'read_at' => null,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        $this->artisan('notifikasi:prune', ['--days' => 90])
            ->expectsOutputToContain('1 baris')
            ->assertSuccessful();

        $this->assertDatabaseMissing('notifications', ['id' => 'old-1']);
        $this->assertDatabaseHas('notifications', ['id' => 'new-1']);
    }

    public function test_schedule_terdaftar(): void
    {
        // pastikan ketiga task terjadwal
        $this->artisan('schedule:list')->expectsOutputToContain('kgb:reminder');
        $this->artisan('schedule:list')->expectsOutputToContain('backup:database');
        $this->artisan('schedule:list')->expectsOutputToContain('notifikasi:prune');
    }
}
