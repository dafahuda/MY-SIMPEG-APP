<?php

namespace Tests\Feature;

use App\Models\AktivitasLog;
use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_menambah_cuti_tercatat_di_log(): void
    {
        $adminUnit = \App\Models\UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $adminUnit->id]);
        $this->actingAs($admin);
        [$pegawaiUser, $pegawai] = $this->makePegawai($adminUnit->id);

        $cuti = Cuti::factory()->create(['pegawai_id' => $pegawai->id]);
        $this->assertDatabaseHas('tb_aktivitas_log', [
            'aksi' => 'buat',
            'modul' => 'Cuti',
            'user_id' => $admin->id,
        ]);
    }

    public function test_menghapus_pegawai_tercatat_di_log(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin);

        [$pegawaiUser, $pegawai] = $this->makePegawai(null);
        $nama = $pegawai->nama;
        $pegawai->delete();

        $this->assertDatabaseHas('tb_aktivitas_log', [
            'aksi' => 'hapus',
            'modul' => 'Pegawai',
            'user_id' => $superadmin->id,
        ]);
        $this->assertDatabaseMissing('tb_pegawai', ['id' => $pegawai->id]);
    }

    public function test_halaman_log_hanya_superadmin(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pegawaiUser = User::factory()->create(['role' => 'pegawai']);

        $this->actingAs($admin)->get('/kepegawaian/audit_trail/riwayat_aktivitas')->assertForbidden();
        $this->actingAs($pegawaiUser)->get('/kepegawaian/audit_trail/riwayat_aktivitas')->assertForbidden();
        $this->actingAs($superadmin)->get('/kepegawaian/audit_trail/riwayat_aktivitas')->assertOk();
    }

    private function makePegawai(?int $unitId): array
    {
        $user = User::factory()->create(['role' => 'pegawai']);
        $pegawai = Pegawai::factory()->create(['user_id' => $user->id]);

        return [$user, $pegawai];
    }
}
