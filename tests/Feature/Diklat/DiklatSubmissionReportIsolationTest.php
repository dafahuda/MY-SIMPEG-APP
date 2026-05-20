<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\PengajuanDiklat;
use App\Models\RencanaDiklat;
use App\Services\DiklatGapAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatSubmissionReportIsolationTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_pending_and_revision_submissions_do_not_change_gap_analytics_or_profile_history(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $revisionRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Rencana Isolasi Revisi', '2029');
        $revisionSubmission = $this->createSubmission($revisionRencana, $fixture['pegawaiInScope'], PengajuanDiklat::STATUS_REVISION_REQUESTED);

        $service = new DiklatGapAnalyticsService();
        $summary = $service->summary(
            RencanaDiklat::with('diklat')->where('pegawai_id', $fixture['pegawaiInScope']->id)->get(),
            Diklat::with('rencanaDiklat')->where('pegawai_id', $fixture['pegawaiInScope']->id)->get()
        );

        $this->assertSame(2, $summary['planned_count']);
        $this->assertSame(0, $summary['realized_linked_count']);
        $this->assertSame(2, $summary['not_realized_count']);
        $this->assertSame(0, $summary['out_of_plan_count']);
        $this->assertDatabaseCount('tb_diklat', 0);

        $this->actingAs($fixture['pegawaiInScope']->user)
            ->get(route('profile.pegawai', ['tab' => 'diklat']))
            ->assertOk()
            ->assertDontSee($fixture['submissionInScope']->nomor_sertifikat)
            ->assertDontSee($revisionSubmission->nomor_sertifikat);
    }
}
