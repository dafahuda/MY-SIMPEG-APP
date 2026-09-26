<?php

namespace Tests\Feature;

use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CutiApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function makePegawaiWithUser(string $role = 'pegawai'): array
    {
        $user = User::factory()->create(['role' => $role]);
        $pegawai = Pegawai::factory()->create(['user_id' => $user->id]);
        return [$user, $pegawai];
    }

    public function test_pegawai_cannot_approve_cuti(): void
    {
        [$pegawaiUser, $pegawai] = $this->makePegawaiWithUser('pegawai');
        $cuti = Cuti::factory()->create(['pegawai_id' => $pegawai->id]);

        $response = $this->actingAs($pegawaiUser)
            ->post("/kepegawaian/cuti/approve/{$cuti->id}");

        $response->assertForbidden();
        $this->assertNotSame('disetujui', $cuti->fresh()->status);
    }

    public function test_admin_can_approve_pending_cuti(): void
    {
        $adminUnit = \App\Models\UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $adminUnit->id]);
        [$pegawaiUser, $pegawai] = $this->makePegawaiWithUser('pegawai');
        $pegawai->update(['unit_kerja_id' => $adminUnit->id]);
        $cuti = Cuti::factory()->create(['pegawai_id' => $pegawai->id, 'status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post("/kepegawaian/cuti/approve/{$cuti->id}");

        $response->assertRedirect();
        $cuti->refresh();
        $this->assertSame('disetujui', $cuti->status);
        $this->assertNotNull($cuti->approved_at);
        $this->assertSame($admin->id, $cuti->approved_by);
    }

    public function test_admin_cannot_approve_cuti_outside_their_unit(): void
    {
        $adminUnit = \App\Models\UnitKerja::factory()->create();
        $otherUnit = \App\Models\UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $adminUnit->id]);
        [$pegawaiUser, $pegawai] = $this->makePegawaiWithUser('pegawai');
        $pegawai->update(['unit_kerja_id' => $otherUnit->id]);
        $cuti = Cuti::factory()->create(['pegawai_id' => $pegawai->id, 'status' => 'pending']);

        $response = $this->actingAs($admin)
            ->post("/kepegawaian/cuti/approve/{$cuti->id}");

        $response->assertForbidden();
        $this->assertSame('pending', $cuti->fresh()->status);
    }

    public function test_rejection_requires_reason(): void
    {
        $adminUnit = \App\Models\UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $adminUnit->id]);
        [$pegawaiUser, $pegawai] = $this->makePegawaiWithUser('pegawai');
        $pegawai->update(['unit_kerja_id' => $adminUnit->id]);
        $cuti = Cuti::factory()->create(['pegawai_id' => $pegawai->id, 'status' => 'pending']);

        // Tanpa alasan → gagal validasi
        $response = $this->actingAs($admin)
            ->post("/kepegawaian/cuti/reject/{$cuti->id}", ['alasan_penolakan' => '']);
        $response->assertSessionHasErrors('alasan_penolakan');
        $this->assertSame('pending', $cuti->fresh()->status);

        // Dengan alasan → ditolak
        $response = $this->actingAs($admin)
            ->post("/kepegawaian/cuti/reject/{$cuti->id}", ['alasan_penolakan' => 'Jadwal kerja sedang padat bulan ini']);
        $response->assertRedirect();
        $this->assertSame('ditolak', $cuti->fresh()->status);
        $this->assertSame('Jadwal kerja sedang padat bulan ini', $cuti->fresh()->alasan_penolakan);
    }

    public function test_pegawai_only_sees_own_cuti_in_index(): void
    {
        [$pegawaiUser, $pegawai] = $this->makePegawaiWithUser('pegawai');
        [$otherUser, $otherPegawai] = $this->makePegawaiWithUser('pegawai');
        Cuti::factory()->count(2)->create(['pegawai_id' => $pegawai->id]);
        Cuti::factory()->create(['pegawai_id' => $otherPegawai->id]);

        $response = $this->actingAs($pegawaiUser)->get('/kepegawaian/cuti');

        $response->assertOk();
        // Hanya 2 cuti milik sendiri yang tampil (bukan milik pegawai lain)
        $this->assertSame(2, substr_count($response->getContent(), 'download_surat_cuti/'));
    }
}
