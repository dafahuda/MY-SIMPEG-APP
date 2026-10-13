<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_laporan_tpp_dibatasi_enkali_per_menit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        for ($i = 1; $i <= 6; $i++) {
            $this->assertLessThan(429, $this->get('/tpp/exportExcelLaporanTpp')->getStatusCode());
        }

        $this->get('/tpp/exportExcelLaporanTpp')->assertStatus(429);
    }

    public function test_download_backup_dibatasi_tigakali_per_menit(): void
    {
        // Route backup dibatasi role superadmin.
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($superadmin);

        for ($i = 1; $i <= 3; $i++) {
            $this->assertLessThan(429, $this->get('/backup_data/download')->getStatusCode());
        }

        $this->get('/backup_data/download')->assertStatus(429);
    }
}
