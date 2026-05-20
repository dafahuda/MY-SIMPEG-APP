<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\PengajuanDiklat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatVerificationApprovalTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_admin_approves_pending_evidence_into_official_realization(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $submission = $fixture['submissionInScope'];

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $submission->id))
            ->assertRedirect(route('diklat_verifikasi.index'));

        $submission->refresh();
        $diklat = Diklat::query()->where('rencana_diklat_id', $fixture['rencanaInScope']->id)->firstOrFail();

        $this->assertTrue($submission->isApproved());
        $this->assertSame($diklat->id, $submission->diklat_id);
        $this->assertSame($fixture['admin']->id, $submission->verified_by);
        $this->assertNotNull($submission->verified_at);
        $this->assertSame($fixture['pegawaiInScope']->id, $diklat->pegawai_id);
        $this->assertSame('Rencana Pengajuan In Scope', $diklat->nama_diklat);
        $this->assertSame('realized', $fixture['rencanaInScope']->refresh()->status);
        $this->assertDatabaseCount('tb_diklat', 1);
    }

    public function test_approval_is_denied_for_out_of_unit_admin_stale_cancelled_realized_and_wrong_year(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $fixture['submissionOutOfScope']->id))
            ->assertNotFound();

        $approvedRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Approval Stale Diklat', '2029');
        $approvedSubmission = $this->createSubmission($approvedRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_APPROVED);
        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $approvedSubmission->id))
            ->assertSessionHasErrors('status');

        $cancelledRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Approval Cancelled Diklat', '2030');
        $cancelledRencana->update(['status' => 'cancelled']);
        $cancelledSubmission = $this->createSubmission($cancelledRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_PENDING);
        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $cancelledSubmission->id))
            ->assertSessionHasErrors('rencana_diklat_id');

        $realizedRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Approval Realized Diklat', '2031');
        $realizedSubmission = $this->createSubmission($realizedRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_PENDING);
        Diklat::create([
            'pegawai_id' => $fixture['pegawaiInScope']->id,
            'rencana_diklat_id' => $realizedRencana->id,
            'nama_diklat' => 'Approval Realized Diklat',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => 'I',
            'tahun' => '2031',
            'no_sttpp' => 'CERT-REALIZED-APPROVAL',
            'tgl_sttpp' => '2031-03-01',
            'file_sertifikat_diklat' => '/storage/document/realized.pdf',
        ]);
        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $realizedSubmission->id))
            ->assertSessionHasErrors('rencana_diklat_id');

        $wrongYearRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Approval Wrong Year Diklat', '2032');
        $wrongYearSubmission = $this->createSubmission($wrongYearRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_PENDING);
        $wrongYearSubmission->update(['tanggal_sertifikat' => '2034-03-01']);
        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $wrongYearSubmission->id))
            ->assertSessionHasErrors('tahun');
    }
}
