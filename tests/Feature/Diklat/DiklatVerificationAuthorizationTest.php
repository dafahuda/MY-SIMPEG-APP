<?php

namespace Tests\Feature\Diklat;

use App\Models\PengajuanDiklat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatVerificationAuthorizationTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_pegawai_cannot_open_verification_detail(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['pegawaiInScope']->user)
            ->get(route('diklat_verifikasi.show', $fixture['submissionInScope']->id))
            ->assertForbidden();
    }

    public function test_admin_cannot_open_out_of_unit_submission_detail(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['admin'])
            ->get(route('diklat_verifikasi.show', $fixture['submissionOutOfScope']->id))
            ->assertNotFound();
    }

    public function test_pending_detail_shows_verification_actions(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['admin'])
            ->get(route('diklat_verifikasi.show', $fixture['submissionInScope']->id))
            ->assertOk()
            ->assertSee('Detail Verifikasi Pengajuan')
            ->assertSee('Data Pegawai')
            ->assertSee('Rencana dan Bukti Diklat')
            ->assertSee('Keputusan Admin')
            ->assertSee('Setujui')
            ->assertSee('Minta Revisi')
            ->assertSee('data-testid="diklat-verifikasi-actions"', false);
    }

    public function test_non_pending_detail_hides_verification_actions(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $approvedRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Rencana Sudah Disetujui', '2029');
        $approvedSubmission = $this->createSubmission($approvedRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_APPROVED);

        $this->actingAs($fixture['admin'])
            ->get(route('diklat_verifikasi.show', $approvedSubmission->id))
            ->assertOk()
            ->assertSee('Terverifikasi')
            ->assertSee('Pengajuan ini tidak menunggu verifikasi.')
            ->assertDontSee('data-testid="diklat-verifikasi-actions"', false);
    }
}
