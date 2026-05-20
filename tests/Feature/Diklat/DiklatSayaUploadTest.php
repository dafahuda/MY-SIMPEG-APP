<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\PengajuanDiklat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiklatSayaUploadTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_diklat_saya_index_shows_admin_assignment_explanation_and_summary_counts(): void
    {
        $unit = $this->createSubmissionUnit('Unit Ringkasan Mandiri');
        $pegawai = $this->createSubmissionPegawai('pegawai-ringkasan-mandiri', $unit);
        $notSubmitted = $this->createAssignedSubmissionRencana($pegawai, 'Ringkasan Belum Diajukan');
        $pending = $this->createAssignedSubmissionRencana($pegawai, 'Ringkasan Menunggu Verifikasi');
        $revision = $this->createAssignedSubmissionRencana($pegawai, 'Ringkasan Perlu Revisi');
        $official = $this->createAssignedSubmissionRencana($pegawai, 'Ringkasan Resmi');

        $this->createSubmission($pending, $pegawai, PengajuanDiklat::STATUS_PENDING);
        $this->createSubmission($revision, $pegawai, PengajuanDiklat::STATUS_REVISION_REQUESTED);
        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $official->id,
            'nama_diklat' => 'Ringkasan Resmi',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => 'I',
            'tahun' => '2028',
            'no_sttpp' => 'CERT-RINGKASAN-001',
            'tgl_sttpp' => '2028-03-01',
            'file_sertifikat_diklat' => '/storage/document/ringkasan.pdf',
        ]);

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSee('Rencana diklat ditugaskan oleh admin/instansi')
            ->assertSeeHtml('data-testid="diklat-saya-summary-cards"')
            ->assertSee('Rencana Ditugaskan')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Perlu Revisi')
            ->assertSee('Riwayat Resmi')
            ->assertSeeInOrder(['Rencana Ditugaskan', '4'])
            ->assertSeeInOrder(['Menunggu Verifikasi', '1'])
            ->assertSeeInOrder(['Perlu Revisi', '1'])
            ->assertSeeInOrder(['Riwayat Resmi', '1'])
            ->assertSee($notSubmitted->nama_diklat_rencana);
    }

    public function test_pegawai_uploads_evidence_for_assigned_rencana_without_creating_official_diklat(): void
    {
        $unit = $this->createSubmissionUnit('Unit Upload Mandiri');
        $pegawai = $this->createSubmissionPegawai('pegawai-upload-mandiri', $unit);
        $rencana = $this->createAssignedSubmissionRencana($pegawai, 'Upload Mandiri Diklat');
        $upload = $this->fakeSubmissionEvidenceUpload('sertifikat-upload.pdf');

        $this->actingAs($pegawai->user)
            ->post(route('diklat_saya.store', $rencana->id), [
                'file_bukti' => $upload,
                'nomor_sertifikat' => 'CERT-UPLOAD-001',
                'tanggal_sertifikat' => '2028-03-01',
                'jumlah_jam_realisasi' => 32,
                'catatan_pegawai' => 'Sudah selesai.',
            ])
            ->assertRedirect(route('diklat_saya.index'));

        $pengajuan = PengajuanDiklat::query()->where('rencana_diklat_id', $rencana->id)->firstOrFail();

        $this->assertTrue($pengajuan->isPending());
        $this->assertSame($pegawai->id, $pengajuan->pegawai_id);
        $this->assertSame('CERT-UPLOAD-001', $pengajuan->nomor_sertifikat);
        $this->assertDatabaseCount('tb_diklat', 0);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $pengajuan->file_bukti));
    }

    public function test_diklat_saya_action_needed_only_shows_not_submitted_and_revision_requested_items(): void
    {
        $unit = $this->createSubmissionUnit('Unit Tindakan Mandiri');
        $pegawai = $this->createSubmissionPegawai('pegawai-tindakan-mandiri', $unit);
        $notSubmitted = $this->createAssignedSubmissionRencana($pegawai, 'Tindakan Upload Bukti');
        $pending = $this->createAssignedSubmissionRencana($pegawai, 'Tindakan Menunggu Verifikasi');
        $revision = $this->createAssignedSubmissionRencana($pegawai, 'Tindakan Kirim Revisi');
        $approved = $this->createAssignedSubmissionRencana($pegawai, 'Tindakan Terverifikasi');
        $cancelled = $this->createAssignedSubmissionRencana($pegawai, 'Tindakan Dibatalkan');
        $realized = $this->createAssignedSubmissionRencana($pegawai, 'Tindakan Sudah Resmi');

        $this->createSubmission($pending, $pegawai, PengajuanDiklat::STATUS_PENDING);
        $revisionSubmission = $this->createSubmission($revision, $pegawai, PengajuanDiklat::STATUS_REVISION_REQUESTED);
        $revisionSubmission->update(['catatan_verifikator' => 'Unggah ulang bukti yang lebih jelas.']);
        $this->createSubmission($approved, $pegawai, PengajuanDiklat::STATUS_APPROVED);
        $cancelled->update(['status' => 'cancelled']);
        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $realized->id,
            'nama_diklat' => 'Tindakan Sudah Resmi',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => 'I',
            'tahun' => '2028',
            'no_sttpp' => 'CERT-TINDAKAN-001',
            'tgl_sttpp' => '2028-03-01',
            'file_sertifikat_diklat' => '/storage/document/tindakan.pdf',
        ]);

        $response = $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSeeHtml('data-testid="diklat-saya-perlu-tindakan"')
            ->assertSee($notSubmitted->nama_diklat_rencana)
            ->assertSee($revision->nama_diklat_rencana)
            ->assertSee('Upload Bukti')
            ->assertSee('Kirim Revisi')
            ->assertSee('Unggah ulang bukti yang lebih jelas.')
            ->assertSeeHtml('href="' . route('diklat_saya.show', $notSubmitted->id) . '"')
            ->assertSeeHtml('href="' . route('diklat_saya.show', $revision->id) . '"');

        $section = str($response->getContent())
            ->between('data-testid="diklat-saya-perlu-tindakan"', 'data-testid="diklat-saya-rencana-table"')
            ->toString();

        $this->assertStringNotContainsString($pending->nama_diklat_rencana, $section);
        $this->assertStringNotContainsString($approved->nama_diklat_rencana, $section);
        $this->assertStringNotContainsString($cancelled->nama_diklat_rencana, $section);
        $this->assertStringNotContainsString($realized->nama_diklat_rencana, $section);
    }

    public function test_assigned_plan_table_uses_state_aware_action_labels(): void
    {
        $unit = $this->createSubmissionUnit('Unit Label Aksi Mandiri');
        $pegawai = $this->createSubmissionPegawai('pegawai-label-aksi-mandiri', $unit);
        $notSubmitted = $this->createAssignedSubmissionRencana($pegawai, 'Label Upload Bukti');
        $pending = $this->createAssignedSubmissionRencana($pegawai, 'Label Lihat Status');
        $revision = $this->createAssignedSubmissionRencana($pegawai, 'Label Kirim Revisi');
        $approved = $this->createAssignedSubmissionRencana($pegawai, 'Label Terverifikasi');
        $cancelled = $this->createAssignedSubmissionRencana($pegawai, 'Label Tidak Tersedia');

        $this->createSubmission($pending, $pegawai, PengajuanDiklat::STATUS_PENDING);
        $this->createSubmission($revision, $pegawai, PengajuanDiklat::STATUS_REVISION_REQUESTED);
        $this->createSubmission($approved, $pegawai, PengajuanDiklat::STATUS_APPROVED);
        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $approved->id,
            'nama_diklat' => 'Label Terverifikasi',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => 'I',
            'tahun' => '2028',
            'no_sttpp' => 'CERT-LABEL-001',
            'tgl_sttpp' => '2028-03-01',
            'file_sertifikat_diklat' => '/storage/document/label.pdf',
        ]);
        $cancelled->update(['status' => 'cancelled']);

        $response = $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSeeHtml('data-testid="diklat-saya-rencana-table"');

        $table = str($response->getContent())
            ->between('data-testid="diklat-saya-rencana-table"', 'data-testid="diklat-saya-riwayat-table"')
            ->toString();

        $this->assertStringContainsString('Upload Bukti', $table);
        $this->assertStringContainsString('Lihat Status', $table);
        $this->assertStringContainsString('Kirim Revisi', $table);
        $this->assertStringContainsString('Terverifikasi', $table);
        $this->assertStringContainsString('Tidak Tersedia', $table);
        $this->assertStringContainsString('href="' . route('diklat_saya.show', $notSubmitted->id) . '"', $table);
        $this->assertStringContainsString('href="' . route('diklat_saya.show', $pending->id) . '"', $table);
        $this->assertStringContainsString('href="' . route('diklat_saya.show', $revision->id) . '"', $table);
        $this->assertStringContainsString('href="' . route('diklat_saya.show', $approved->id) . '"', $table);
        $this->assertStringContainsString('href="' . route('diklat_saya.show', $cancelled->id) . '"', $table);
    }

    public function test_detail_page_explains_each_submission_state_and_preserves_form_rules(): void
    {
        $unit = $this->createSubmissionUnit('Unit Detail Status Mandiri');
        $pegawai = $this->createSubmissionPegawai('pegawai-detail-status-mandiri', $unit);
        $notSubmitted = $this->createAssignedSubmissionRencana($pegawai, 'Detail Belum Diajukan');
        $pending = $this->createAssignedSubmissionRencana($pegawai, 'Detail Menunggu Verifikasi');
        $revision = $this->createAssignedSubmissionRencana($pegawai, 'Detail Perlu Revisi');
        $official = $this->createAssignedSubmissionRencana($pegawai, 'Detail Terverifikasi');
        $cancelled = $this->createAssignedSubmissionRencana($pegawai, 'Detail Tidak Tersedia');

        $this->createSubmission($pending, $pegawai, PengajuanDiklat::STATUS_PENDING);
        $revisionSubmission = $this->createSubmission($revision, $pegawai, PengajuanDiklat::STATUS_REVISION_REQUESTED);
        $revisionSubmission->update(['catatan_verifikator' => 'Perbaiki nomor sertifikat.']);
        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $official->id,
            'nama_diklat' => 'Detail Terverifikasi',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => 'I',
            'tahun' => '2028',
            'no_sttpp' => 'CERT-DETAIL-001',
            'tgl_sttpp' => '2028-03-01',
            'file_sertifikat_diklat' => '/storage/document/detail.pdf',
        ]);
        $cancelled->update(['status' => 'cancelled']);

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.show', $notSubmitted->id))
            ->assertOk()
            ->assertSeeHtml('data-testid="diklat-saya-state-message"')
            ->assertSee('Belum diajukan')
            ->assertSee('Unggah bukti setelah diklat selesai')
            ->assertSeeHtml('data-testid="diklat-saya-upload-form"')
            ->assertSee('Kirim Bukti');

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.show', $pending->id))
            ->assertOk()
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Pengajuan belum dapat diubah sampai admin meminta revisi')
            ->assertDontSeeHtml('data-testid="diklat-saya-upload-form"');

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.show', $revision->id))
            ->assertOk()
            ->assertSee('Perlu Revisi')
            ->assertSee('Perbaiki nomor sertifikat.')
            ->assertSeeHtml('data-testid="diklat-saya-upload-form"')
            ->assertSee('Kirim Revisi');

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.show', $official->id))
            ->assertOk()
            ->assertSee('Terverifikasi')
            ->assertSee('riwayat diklat resmi')
            ->assertDontSeeHtml('data-testid="diklat-saya-upload-form"');

        $this->actingAs($pegawai->user)
            ->get(route('diklat_saya.show', $cancelled->id))
            ->assertOk()
            ->assertSee('Tidak Tersedia')
            ->assertSee('Rencana diklat ini dibatalkan')
            ->assertDontSeeHtml('data-testid="diklat-saya-upload-form"');
    }

    public function test_pegawai_cannot_upload_for_cancelled_other_employee_or_realized_rencana(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $pegawai = $fixture['pegawaiInScope'];
        $cancelledRencana = $this->createAssignedSubmissionRencana($pegawai, 'Upload Cancelled Diklat');
        $cancelledRencana->update(['status' => 'cancelled']);
        $realizedRencana = $this->createAssignedSubmissionRencana($pegawai, 'Upload Realized Diklat');
        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $realizedRencana->id,
            'nama_diklat' => 'Upload Realized Diklat',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => 'I',
            'tahun' => '2028',
            'no_sttpp' => 'CERT-REALIZED-001',
            'tgl_sttpp' => '2028-03-01',
            'file_sertifikat_diklat' => 'sertifikat/realized.pdf',
        ]);

        $this->actingAs($pegawai->user)
            ->post(route('diklat_saya.store', $fixture['rencanaOutOfScope']->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('out-scope.pdf'),
            ])
            ->assertNotFound();

        $this->actingAs($pegawai->user)
            ->post(route('diklat_saya.store', $cancelledRencana->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('cancelled.pdf'),
            ])
            ->assertSessionHasErrors('rencana_diklat_id');

        $this->actingAs($pegawai->user)
            ->post(route('diklat_saya.store', $realizedRencana->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('realized.pdf'),
            ])
            ->assertSessionHasErrors('rencana_diklat_id');
    }

    public function test_pegawai_revises_only_when_revision_is_requested(): void
    {
        $unit = $this->createSubmissionUnit('Unit Revisi Mandiri');
        $pegawai = $this->createSubmissionPegawai('pegawai-revisi-mandiri', $unit);
        $rencana = $this->createAssignedSubmissionRencana($pegawai, 'Revisi Mandiri Diklat');
        $pengajuan = $this->createSubmission($rencana, $pegawai, PengajuanDiklat::STATUS_REVISION_REQUESTED);
        Storage::fake('public');
        Storage::disk('public')->put('document/lama.pdf', 'lama');
        $pengajuan->update([
            'file_bukti' => '/storage/document/lama.pdf',
            'catatan_verifikator' => 'Perbaiki dokumen.',
            'verified_by' => $this->createSubmissionUser('admin-revisi-mandiri', 'admin', $unit)->id,
            'verified_at' => now(),
            'revision_count' => 0,
        ]);

        $this->actingAs($pegawai->user)
            ->put(route('diklat_saya.update', $pengajuan->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('revisi.pdf'),
                'nomor_sertifikat' => 'CERT-REV-001',
                'tanggal_sertifikat' => '2028-03-01',
                'jumlah_jam_realisasi' => 32,
            ])
            ->assertRedirect(route('diklat_saya.index'));

        $pengajuan->refresh();

        $this->assertTrue($pengajuan->isPending());
        $this->assertSame(1, $pengajuan->revision_count);
        $this->assertSame('CERT-REV-001', $pengajuan->nomor_sertifikat);
        $this->assertNull($pengajuan->catatan_verifikator);
        $this->assertNull($pengajuan->verified_by);
        $this->assertNull($pengajuan->verified_at);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $pengajuan->file_bukti));
        Storage::disk('public')->assertMissing('document/lama.pdf');

        $this->actingAs($pegawai->user)
            ->put(route('diklat_saya.update', $pengajuan->id), [
                'file_bukti' => $this->fakeSubmissionEvidenceUpload('pending.pdf'),
            ])
            ->assertSessionHasErrors('status');
    }
}
