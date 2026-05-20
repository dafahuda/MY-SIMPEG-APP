<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\RencanaDiklat;
use App\Services\DiklatGapAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatSubmissionApprovedAnalyticsTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_approved_submission_appears_in_analytics_and_profile_through_official_diklat(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $submission = $fixture['submissionInScope'];
        $submission->update([
            'nomor_sertifikat' => 'CERT-APPROVED-ANALYTICS',
            'tanggal_sertifikat' => '2028-03-01',
            'jumlah_jam_realisasi' => 36,
        ]);

        $this->actingAs($fixture['admin'])
            ->post(route('diklat_verifikasi.approve', $submission->id))
            ->assertRedirect(route('diklat_verifikasi.index'));

        $service = new DiklatGapAnalyticsService();
        $summary = $service->summary(
            RencanaDiklat::with('diklat')->where('pegawai_id', $fixture['pegawaiInScope']->id)->get(),
            Diklat::with('rencanaDiklat')->where('pegawai_id', $fixture['pegawaiInScope']->id)->get()
        );

        $this->assertSame(1, $summary['planned_count']);
        $this->assertSame(1, $summary['realized_linked_count']);
        $this->assertSame(0, $summary['not_realized_count']);
        $this->assertSame(36, $summary['realized_linked_hours']);
        $this->assertSame('realized', $fixture['rencanaInScope']->refresh()->status);
        $this->assertDatabaseHas('tb_diklat', [
            'rencana_diklat_id' => $fixture['rencanaInScope']->id,
            'no_sttpp' => 'CERT-APPROVED-ANALYTICS',
        ]);

        $this->actingAs($fixture['pegawaiInScope']->user)
            ->get(route('profile.pegawai', ['tab' => 'diklat']))
            ->assertOk()
            ->assertSee('CERT-APPROVED-ANALYTICS')
            ->assertSee('Rencana Pengajuan In Scope');
    }
}
