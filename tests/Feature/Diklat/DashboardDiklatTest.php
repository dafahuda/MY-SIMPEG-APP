<?php

namespace Tests\Feature\Diklat;

use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardDiklatTest extends TestCase
{
    use RefreshDatabase;
    use DiklatGapReportFixtures;

    public function test_admin_dashboard_shows_current_year_diklat_analytics_from_own_unit_scope(): void
    {
        $currentYear = (string) now()->year;

        $unitA = UnitKerja::create([
            'nama_unit' => 'BKPSDM Dashboard A',
            'alamat' => 'Jl. Dashboard A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'BKPSDM Dashboard B',
            'alamat' => 'Jl. Dashboard B',
        ]);

        $admin = $this->createAdmin($unitA);
        $pegawaiA = $this->createPegawai('pegawai-dashboard-a', 'pegawai.dashboard.a@example.test', 'Pegawai Dashboard A', $unitA);
        $pegawaiB = $this->createPegawai('pegawai-dashboard-b', 'pegawai.dashboard.b@example.test', 'Pegawai Dashboard B', $unitB);

        $this->createRencana($pegawaiA, $currentYear, 'Rencana Dashboard Planned', 'planned', 40);
        $rencanaARealized = $this->createRencana($pegawaiA, $currentYear, 'Rencana Dashboard Realized', 'realized', 30);
        $this->createDiklat($pegawaiA, $rencanaARealized, $currentYear, 'Diklat Dashboard Realized', 28);
        $this->createDiklat($pegawaiA, null, $currentYear, 'Diklat Dashboard Out of Plan', 16);

        $rencanaBRealized = $this->createRencana($pegawaiB, $currentYear, 'Rencana Unit B Realized', 'realized', 24);
        $this->createDiklat($pegawaiB, $rencanaBRealized, $currentYear, 'Diklat Unit B Realized', 24);
        $this->createDiklat($pegawaiB, null, $currentYear, 'Diklat Unit B Out of Plan', 12);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Analitik Diklat Tahun ' . $currentYear)
            ->assertSee('Ringkasan plan vs realisasi untuk BKPSDM Dashboard A.')
            ->assertSeeHtml("data-testid='dashboard-diklat-planned' data-value='2'")
            ->assertSeeHtml("data-testid='dashboard-diklat-realized' data-value='1'")
            ->assertSeeHtml("data-testid='dashboard-diklat-not-realized' data-value='1'")
            ->assertSeeHtml("data-testid='dashboard-diklat-out-of-plan' data-value='1'")
            ->assertSeeHtml("data-testid='dashboard-diklat-status-chart'");
    }

    public function test_pegawai_hitting_dashboard_is_redirected_to_profile_and_never_sees_admin_cards(): void
    {
        $unit = UnitKerja::create([
            'nama_unit' => 'Unit Pegawai Dashboard',
            'alamat' => 'Jl. Pegawai Dashboard',
        ]);

        $pegawai = $this->createPegawai('pegawai-dashboard-self', 'pegawai.dashboard.self@example.test', 'Pegawai Dashboard Self', $unit);

        $this->actingAs($pegawai->user)
            ->get(route('dashboard'))
            ->assertRedirect(route('profile.pegawai'));

        $this->actingAs($pegawai->user)
            ->followingRedirects()
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pegawai Dashboard Self')
            ->assertDontSee("data-testid='dashboard-diklat-planned'", false)
            ->assertDontSee("data-testid='dashboard-diklat-realized'", false)
            ->assertDontSee("data-testid='dashboard-diklat-not-realized'", false)
            ->assertDontSee("data-testid='dashboard-diklat-out-of-plan'", false);
    }

    public function test_dashboard_excludes_linked_realizations_outside_current_year_summary(): void
    {
        $currentYear = (string) now()->year;
        $nextYear = (string) ((int) $currentYear + 1);

        $unit = UnitKerja::create([
            'nama_unit' => 'BKPSDM Dashboard Filter Tahun',
            'alamat' => 'Jl. Dashboard Filter Tahun',
        ]);

        $admin = $this->createAdmin($unit);
        $pegawai = $this->createPegawai('pegawai-dashboard-filter', 'pegawai.dashboard.filter@example.test', 'Pegawai Dashboard Filter', $unit);

        $rencanaLintasTahun = $this->createRencana($pegawai, $currentYear, 'Rencana Lintas Tahun Dashboard', 'realized', 30);
        $this->createDiklat($pegawai, $rencanaLintasTahun, $nextYear, 'Diklat Lintas Tahun Dashboard', 28);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertSeeHtml("data-testid='dashboard-diklat-planned' data-value='1'")
            ->assertSeeHtml("data-testid='dashboard-diklat-realized' data-value='0'")
            ->assertSeeHtml("data-testid='dashboard-diklat-not-realized' data-value='1'")
            ->assertSeeHtml("data-testid='dashboard-diklat-out-of-plan' data-value='0'");
    }

    private function createAdmin(UnitKerja $unitKerja): User
    {
        return User::create([
            'username' => 'admin-dashboard-diklat',
            'name' => 'Admin Dashboard Diklat',
            'email' => 'admin.dashboard.diklat@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);
    }
}
