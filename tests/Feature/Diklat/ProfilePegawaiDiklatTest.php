<?php

namespace Tests\Feature\Diklat;

use App\Models\UnitKerja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePegawaiDiklatTest extends TestCase
{
    use RefreshDatabase;
    use DiklatGapReportFixtures;

    public function test_profile_page_shows_only_logged_in_pegawai_diklat_scope(): void
    {
        $currentYear = (string) now()->year;

        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit Profile Diklat A',
            'alamat' => 'Jl. Profile A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit Profile Diklat B',
            'alamat' => 'Jl. Profile B',
        ]);

        $pegawaiA = $this->createPegawai('pegawai-profile-diklat-a', 'pegawai.profile.diklat.a@example.test', 'Pegawai Profile Diklat A', $unitA);
        $pegawaiB = $this->createPegawai('pegawai-profile-diklat-b', 'pegawai.profile.diklat.b@example.test', 'Pegawai Profile Diklat B', $unitB);

        $this->createRencana($pegawaiA, $currentYear, 'Rencana A Self Planned', 'planned', 40);
        $rencanaRealized = $this->createRencana($pegawaiA, $currentYear, 'Rencana A Self Realized', 'realized', 30);
        $this->createDiklat($pegawaiA, $rencanaRealized, $currentYear, 'Diklat A Self Linked', 24);
        $this->createDiklat($pegawaiA, null, $currentYear, 'Diklat A Self Out of Plan', 16);

        $this->createRencana($pegawaiB, $currentYear, 'Rencana B Hidden', 'realized', 50);
        $this->createDiklat($pegawaiB, null, $currentYear, 'Diklat B Hidden', 20);

        $response = $this->actingAs($pegawaiA->user)->get(route('profile.pegawai'));

        $response->assertOk()
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary"')
            ->assertSeeHtml('data-testid="profile-diklat-plan-table"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-planned" data-value="2"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-realized" data-value="1"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-not-realized" data-value="1"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-out-of-plan" data-value="1"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-hour-gap" data-value="46"')
            ->assertSee('Rencana A Self Planned')
            ->assertSee('Rencana A Self Realized')
            ->assertSee('Diklat A Self Linked')
            ->assertSee('Diklat A Self Out of Plan')
            ->assertDontSee('Rencana B Hidden')
            ->assertDontSee('Diklat B Hidden');
    }

    public function test_profile_summary_excludes_linked_realization_outside_active_year(): void
    {
        $currentYear = (string) now()->year;
        $nextYear = (string) ((int) $currentYear + 1);

        $unit = UnitKerja::create([
            'nama_unit' => 'Unit Profile Filter Tahun',
            'alamat' => 'Jl. Profile Filter Tahun',
        ]);

        $pegawai = $this->createPegawai('pegawai-profile-filter', 'pegawai.profile.filter@example.test', 'Pegawai Profile Filter', $unit);

        $rencanaRealized = $this->createRencana($pegawai, $currentYear, 'Rencana Profile Tahun Lain', 'realized', 30);
        $this->createDiklat($pegawai, $rencanaRealized, $nextYear, 'Diklat Profile Tahun Lain', 24);

        $response = $this->actingAs($pegawai->user)->get(route('profile.pegawai'));

        $response->assertOk()
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-planned" data-value="1"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-realized" data-value="0"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-not-realized" data-value="1"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-out-of-plan" data-value="0"')
            ->assertSeeHtml('data-testid="profile-diklat-gap-summary-hour-gap" data-value="30"');
    }
}
