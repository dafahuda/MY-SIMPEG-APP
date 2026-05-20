<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\PengajuanDiklat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiklatSubmissionWorkflowTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_full_assignment_upload_revision_and_approval_workflow(): void
    {
        Storage::fake('public');
        $unit = $this->createSubmissionUnit('Unit Workflow Diklat');
        $admin = $this->createSubmissionUser('admin-workflow-diklat', 'admin', $unit);
        $pegawai = $this->createSubmissionPegawai('pegawai-workflow-diklat', $unit);
        $rencana = $this->createAssignedSubmissionRencana($pegawai, 'Workflow Diklat Terpadu', '2028');

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSee('Workflow Diklat Terpadu')
            ->assertSee('Diklat Saya')
            ->assertSeeHtml('data-testid="diklat-saya-rencana-table"');

        $this->actingAs($pegawai->user)
            ->post(route('diklat_saya.store', $rencana->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('workflow-awal.pdf'),
                'nomor_sertifikat' => 'CERT-WORKFLOW-001',
                'tanggal_sertifikat' => '2028-03-01',
                'jumlah_jam_realisasi' => 32,
                'catatan_pegawai' => 'Bukti awal selesai.',
            ])
            ->assertRedirect(route('diklat_saya.index'));

        $submission = PengajuanDiklat::query()->where('rencana_diklat_id', $rencana->id)->firstOrFail();
        $this->assertTrue($submission->isPending());

        $this->actingAs($admin)
            ->get(route('diklat_verifikasi.show', $submission->id))
            ->assertOk()
            ->assertSee('Menunggu Verifikasi')
            ->assertSeeHtml('data-testid="diklat-verifikasi-actions"')
            ->assertSee('Setujui')
            ->assertSee('Minta Revisi');

        $this->actingAs($admin)
            ->post(route('diklat_verifikasi.reject', $submission->id), [
                'catatan_verifikator' => 'Sertifikat belum jelas.',
            ])
            ->assertRedirect(route('diklat_verifikasi.index'));

        $submission->refresh();
        $this->assertTrue($submission->needsRevision());
        $this->assertSame('Sertifikat belum jelas.', $submission->catatan_verifikator);

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.show', $rencana->id))
            ->assertOk()
            ->assertSee('Perlu Revisi')
            ->assertSee('Sertifikat belum jelas.')
            ->assertSeeHtml('data-testid="diklat-saya-upload-form"');

        $this->actingAs($pegawai->user)
            ->put(route('diklat_saya.update', $submission->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('workflow-revisi.pdf'),
                'nomor_sertifikat' => 'CERT-WORKFLOW-REV-001',
                'tanggal_sertifikat' => '2028-04-01',
                'jumlah_jam_realisasi' => 40,
                'catatan_pegawai' => 'Bukti revisi sudah diperbaiki.',
            ])
            ->assertRedirect(route('diklat_saya.index'));

        $submission->refresh();
        $this->assertTrue($submission->isPending());
        $this->assertSame(1, $submission->revision_count);

        $this->actingAs($admin)
            ->post(route('diklat_verifikasi.approve', $submission->id))
            ->assertRedirect(route('diklat_verifikasi.index'));

        $submission->refresh();
        $diklat = Diklat::query()->where('rencana_diklat_id', $rencana->id)->firstOrFail();

        $this->assertTrue($submission->isApproved());
        $this->assertSame($diklat->id, $submission->diklat_id);
        $this->assertSame('realized', $rencana->refresh()->status);
        $this->assertSame('CERT-WORKFLOW-REV-001', $diklat->no_sttpp);
        $this->assertDatabaseCount('tb_diklat', 1);

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSee('CERT-WORKFLOW-REV-001')
            ->assertSee('Workflow Diklat Terpadu');
    }

    public function test_workflow_role_boundaries_remain_blocked(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['pegawaiInScope']->user)
            ->post(route('diklat_verifikasi.approve', $fixture['submissionInScope']->id))
            ->assertForbidden();

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_saya.store', $fixture['rencanaInScope']->id))
            ->assertForbidden();

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $fixture['submissionOutOfScope']->id))
            ->assertNotFound();
    }
}
