<?php

namespace Tests\Feature\AdminSuperadminUiux;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\Feature\Security\Support\RoleMatrixFixtures;
use Tests\TestCase;

class RouteAccessMatrixTest extends TestCase
{
    use RoleMatrixFixtures;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('superadmin', fn (User $user): bool => $user->role === 'superadmin');
    }

    public function test_guest_is_redirected_to_login_for_authenticated_route_families(): void
    {
        foreach ($this->guestRoutes() as $path) {
            $this->get($path)->assertRedirect('/login');
        }
    }

    public function test_pegawai_is_forbidden_or_safely_redirected_from_admin_and_system_routes(): void
    {
        [$pegawaiUser] = $this->createPegawaiUser('matrix-pegawai', 'matrix.pegawai@example.test', 'Pegawai Matrix');

        foreach ($this->pegawaiBlockedRoutes() as $path) {
            $response = $this->actingAs($pegawaiUser)->get($path);

            $this->assertContains(
                $response->getStatusCode(),
                [302, 403],
                "Pegawai route [{$path}] must be forbidden or safely redirected, got [{$response->getStatusCode()}]."
            );
        }

        $this->actingAs($pegawaiUser)
            ->get('/profile_saya')
            ->assertOk();

        $this->actingAs($pegawaiUser)
            ->get(route('diklat_saya.index'))
            ->assertStatus(404);
    }

    public function test_pegawai_report_direct_url_is_forbidden(): void
    {
        [$pegawaiUser] = $this->createPegawaiUser('matrix-pegawai-report', 'matrix.pegawai.report@example.test', 'Pegawai Matrix Report');

        foreach ($this->pegawaiReportRoutes() as $path) {
            $this->actingAs($pegawaiUser)
                ->get($path)
                ->assertForbidden();
        }
    }

    public function test_admin_can_reach_expected_route_families_without_global_system_access(): void
    {
        $admin = $this->createRoleUser('admin', 'matrix-admin', 'matrix.admin@example.test', $this->createUnitKerja()->id);
        $this->createPegawaiUser('matrix-admin-unit-pegawai', 'matrix.admin.unit.pegawai@example.test', 'Pegawai Unit Admin', $admin->unit_kerja_id);

        foreach ($this->adminAllowedRoutes() as $path) {
            $this->assertRouteExistsForPath($path);
        }

        foreach ($this->adminForbiddenRoutes() as $path) {
            $this->actingAs($admin)
                ->get($path)
                ->assertForbidden();
        }
    }

    public function test_superadmin_expected_route_families_are_registered(): void
    {
        foreach ($this->superadminAllowedRoutes() as $path) {
            $this->assertRouteExistsForPath($path);
        }
    }

    private function assertRouteExistsForPath(string $path): void
    {
        $method = app('router')->getRoutes()->match(
            request()->create($path, 'GET')
        )->methods()[0] ?? null;

        $this->assertSame('GET', $method, "Expected GET route [{$path}] to be registered.");
    }

    private function guestRoutes(): array
    {
        return [
            '/dashboard',
            '/data_pegawai',
            '/manajemen_setup/data_user_admin',
            '/manajemen_setup/data_user_pegawai',
            route('rencana_diklat.index'),
            '/kepegawaian/report/diklat_gap',
            '/backup_data',
            route('diklat_saya.index'),
        ];
    }

    private function pegawaiBlockedRoutes(): array
    {
        return [
            '/data_pegawai',
            '/manajemen_setup/data_user_admin',
            '/manajemen_setup/data_user_pegawai',
            route('rencana_diklat.index'),
            '/kepegawaian/report/diklat_gap/unit',
            '/backup_data',
        ];
    }

    private function pegawaiReportRoutes(): array
    {
        return [
            '/report/nominatif?unit_kerja_id=1',
            '/report/nominatif/print?unit_kerja_id=1',
            '/report/duk?unit_kerja_id=1',
            '/report/duk/print?unit_kerja_id=1',
            '/report/bezetting?unit_kerja_id=1',
            '/report/bezetting/print?unit_kerja_id=1',
            '/report/keadaan_pegawai?unit_kerja_id=1',
            '/report/keadaan_pegawai/print?unit_kerja_id=1',
            '/report/pensiun?unit_kerja_id=1',
            '/kepegawaian/report/diklat_gap',
            '/kepegawaian/report/diklat_gap/unit',
            '/kepegawaian/report/diklat_gap/unit/print?tahun_rencana=2026&tahun_realisasi=2027',
            '/kepegawaian/report/diklat_gap/unit/export?tahun_rencana=2026&tahun_realisasi=2027',
        ];
    }

    private function adminAllowedRoutes(): array
    {
        return [
            '/dashboard',
            '/data_pegawai',
            route('rencana_diklat.index'),
            '/kepegawaian/report/diklat_gap',
        ];
    }

    private function adminForbiddenRoutes(): array
    {
        return [
            '/manajemen_setup/data_user_admin',
            '/backup_data',
        ];
    }

    private function superadminAllowedRoutes(): array
    {
        return [
            '/dashboard',
            '/data_pegawai',
            '/manajemen_setup/data_user_admin',
            route('rencana_diklat.index'),
            '/kepegawaian/report/diklat_gap',
            '/backup_data',
        ];
    }

}
