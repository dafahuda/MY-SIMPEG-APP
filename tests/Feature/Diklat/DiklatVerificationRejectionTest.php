<?php

namespace Tests\Feature\Diklat;

use App\Models\PengajuanDiklat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiklatVerificationRejectionTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_admin_rejects_pending_evidence_without_creating_official_realization(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.reject', $fixture['submissionInScope']->id), [
                'catatan_verifikator' => 'Mohon unggah sertifikat yang lebih jelas.',
            ])
            ->assertRedirect(route('diklat_verifikasi.index'));

        $submission = $fixture['submissionInScope']->refresh();

        $this->assertTrue($submission->needsRevision());
        $this->assertSame('Mohon unggah sertifikat yang lebih jelas.', $submission->catatan_verifikator);
        $this->assertSame($fixture['admin']->id, $submission->verified_by);
        $this->assertNotNull($submission->verified_at);
        $this->assertNull($submission->diklat_id);
        $this->assertDatabaseCount('tb_diklat', 0);
    }

    public function test_rejected_submission_can_be_revised_by_employee(): void
    {
        Storage::fake('public');
        $fixture = $this->createSubmissionScopeFixture();
        $submission = $fixture['submissionInScope'];
        Storage::disk('public')->put('document/old-reject.pdf', 'old');
        $submission->update(['file_bukti' => '/storage/document/old-reject.pdf']);

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.reject', $submission->id), [
                'catatan_verifikator' => 'Perlu revisi.',
            ])
            ->assertRedirect(route('diklat_verifikasi.index'));

        $this->actingAs($fixture['pegawaiInScope']->user)
            ->put(route('diklat_saya.update', $submission->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('revisi-sertifikat.pdf'),
                'nomor_sertifikat' => 'CERT-REVISION-001',
                'tanggal_sertifikat' => '2028-04-01',
                'jumlah_jam_realisasi' => 40,
                'catatan_pegawai' => 'Sertifikat sudah diperbaiki.',
            ])
            ->assertRedirect(route('diklat_saya.index'));

        $submission->refresh();

        $this->assertTrue($submission->isPending());
        $this->assertSame(1, $submission->revision_count);
        $this->assertSame('CERT-REVISION-001', $submission->nomor_sertifikat);
        $this->assertDatabaseCount('tb_diklat', 0);
    }

    public function test_rejection_is_denied_for_non_pending_submission(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $approvedRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Reject Approved Diklat', '2029');
        $approvedSubmission = $this->createSubmission($approvedRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_APPROVED);

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.reject', $approvedSubmission->id), [
                'catatan_verifikator' => 'Tidak bisa ditolak.',
            ])
            ->assertSessionHasErrors('status');
    }
}
