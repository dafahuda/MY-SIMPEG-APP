<?php

namespace Tests\Feature\Diklat;

use App\Models\PengajuanDiklat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatVerificationQueueTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_admin_queue_lists_only_scoped_submissions(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['admin'])
            ->get(route('diklat_verifikasi.index'))
            ->assertOk()
            ->assertSee('Verifikasi Pengajuan')
            ->assertSee('Menampilkan')
            ->assertSee('pengajuan')
            ->assertSee('Rencana Pengajuan In Scope')
            ->assertSee('Unit Pengajuan In Scope')
            ->assertSee('Lihat Detail')
            ->assertSee('Menunggu Verifikasi')
            ->assertDontSee('Rencana Pengajuan Out Scope');
    }

    public function test_superadmin_queue_lists_all_submissions_and_filters_status(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $revisionRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Rencana Revisi Pengajuan', '2029');
        $this->createSubmission($revisionRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_REVISION_REQUESTED);

        $this->actingAs($fixture['superadmin'])
            ->get(route('diklat_verifikasi.index'))
            ->assertOk()
            ->assertSee('Rencana Pengajuan In Scope')
            ->assertSee('Rencana Pengajuan Out Scope');

        $this->actingAs($fixture['superadmin'])
            ->get(route('diklat_verifikasi.index', ['status' => PengajuanDiklat::STATUS_REVISION_REQUESTED]))
            ->assertOk()
            ->assertSee('Rencana Revisi Pengajuan')
            ->assertSee('Perlu Revisi')
            ->assertDontSee('Rencana Pengajuan Out Scope');
    }
}
