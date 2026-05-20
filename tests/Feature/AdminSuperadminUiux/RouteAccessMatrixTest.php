<?php

namespace Tests\Feature\AdminSuperadminUiux;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RouteAccessMatrixTest extends TestCase
{
    use RefreshDatabase;

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
            ->assertOk();
    }

    public function test_admin_can_access_unit_scoped_operational_pages_but_not_global_system_pages(): void
    {
        $admin = $this->createRoleUser('admin', 'matrix-admin', 'matrix.admin@example.test', $this->unit()->id);
        $this->createPegawaiUser('matrix-admin-unit-pegawai', 'matrix.admin.unit.pegawai@example.test', 'Pegawai Unit Admin', $admin->unit_kerja_id);

        foreach ($this->adminAllowedRoutes() as $path) {
            $this->actingAs($admin)
                ->get($path)
                ->assertOk();
        }

        foreach ($this->adminForbiddenRoutes() as $path) {
            $this->actingAs($admin)
                ->get($path)
                ->assertForbidden();
        }
    }

    public function test_superadmin_can_access_global_and_system_route_families(): void
    {
        $superadmin = $this->createRoleUser('superadmin', 'matrix-superadmin', 'matrix.superadmin@example.test');
        $this->createPegawaiUser('matrix-global-pegawai', 'matrix.global.pegawai@example.test', 'Pegawai Global');

        foreach ($this->superadminAllowedRoutes() as $path) {
            $this->actingAs($superadmin)
                ->get($path)
                ->assertOk();
        }
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

    private function createRoleUser(string $role, string $username, string $email, ?int $unitKerjaId = null): User
    {
        return User::create([
            'username' => $username,
            'name' => ucfirst($role) . ' Matrix',
            'email' => $email,
            'email_verified_at' => now(),
            'role' => $role,
            'unit_kerja_id' => $unitKerjaId,
            'password' => bcrypt('password'),
        ]);
    }

    private function createPegawaiUser(string $username, string $email, string $nama, ?int $unitKerjaId = null): array
    {
        $unitKerjaId ??= $this->unit()->id;

        $user = $this->createRoleUser('pegawai', $username, $email, $unitKerjaId);

        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerjaId,
            'foto' => 'foto.jpg',
            'nip' => fake()->unique()->numerify('##################'),
            'nik' => fake()->unique()->numerify('################'),
            'nama' => $nama,
            'gelar' => 'S.T.',
            'gelar_depan' => null,
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Belum Nikah',
            'alamat' => 'Jl. Matrix No. 1',
            'no_hp' => '081234567890',
            'email' => $email,
            'email_gov' => $email,
            'no_npwp' => '00.000.000.0-000.000',
            'no_bpjs' => '0000000000000001',
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-MATRIX',
            'no_sk_cpns' => 'SKCPNS-MATRIX',
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-MATRIX',
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'nilai_tpp' => 0,
        ]);

        return [$user, $pegawai];
    }

    private function unit(): UnitKerja
    {
        return UnitKerja::create([
            'nama_unit' => fake()->unique()->words(3, true),
            'alamat' => 'Jl. Unit Matrix',
        ]);
    }
}
