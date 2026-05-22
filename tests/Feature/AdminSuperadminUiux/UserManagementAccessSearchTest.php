<?php

namespace Tests\Feature\AdminSuperadminUiux;

use App\Http\Controllers\UserPegawaiController;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementAccessSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_admin_management_is_superadmin_only_by_direct_url(): void
    {
        $superadmin = $this->createUser('superadmin', 'superadmin-user-admin@example.test');
        $admin = $this->createUser('admin', 'admin-user-admin@example.test', $this->unit('Admin Unit')->id);
        $pegawai = $this->createUser('pegawai', 'pegawai-user-admin@example.test', $this->unit('Pegawai Unit')->id);
        $this->createUser('admin', 'managed-admin@example.test', $admin->unit_kerja_id, 'Managed Admin');

        $this->actingAs($superadmin)
            ->get('/manajemen_setup/data_user_admin')
            ->assertOk()
            ->assertSee('Data User Admin')
            ->assertSee('Managed Admin');

        foreach ([$admin, $pegawai] as $disallowedUser) {
            $this->actingAs($disallowedUser)
                ->get('/manajemen_setup/data_user_admin')
                ->assertForbidden();
        }
    }

    public function test_user_pegawai_index_route_has_single_effective_get_definition(): void
    {
        $matchingRoutes = collect(app('router')->getRoutes()->getRoutes())->filter(function ($route): bool {
            return in_array('GET', $route->methods(), true)
                && $route->uri() === 'manajemen_setup/data_user_pegawai';
        });

        $this->assertCount(1, $matchingRoutes);
        $this->assertSame(UserPegawaiController::class . '@index', $matchingRoutes->first()?->getActionName());
    }

    public function test_user_pegawai_management_blocks_pegawai_and_scopes_admin_to_own_unit(): void
    {
        $unitA = $this->unit('Unit A');
        $unitB = $this->unit('Unit B');

        $superadmin = $this->createUser('superadmin', 'superadmin-user-pegawai@example.test');
        $admin = $this->createUser('admin', 'admin-user-pegawai@example.test', $unitA->id);
        $pegawaiActor = $this->createUser('pegawai', 'pegawai-actor@example.test', $unitA->id, 'Pegawai Actor');
        $unitAPegawai = $this->createUser('pegawai', 'pegawai.unit.a@example.test', $unitA->id, 'Pegawai Unit A');
        $unitBPegawai = $this->createUser('pegawai', 'pegawai.unit.b@example.test', $unitB->id, 'Pegawai Unit B');

        $this->actingAs($superadmin)
            ->get('/manajemen_setup/data_user_pegawai')
            ->assertOk()
            ->assertSee($unitAPegawai->username)
            ->assertSee($unitBPegawai->username);

        $this->actingAs($admin)
            ->get('/manajemen_setup/data_user_pegawai')
            ->assertOk()
            ->assertSee($unitAPegawai->username)
            ->assertDontSee($unitBPegawai->username);

        $this->actingAs($pegawaiActor)
            ->get('/manajemen_setup/data_user_pegawai')
            ->assertForbidden();
    }

    public function test_user_pegawai_search_blocks_pegawai_and_scopes_admin_to_own_unit(): void
    {
        $unitA = $this->unit('Search Unit A');
        $unitB = $this->unit('Search Unit B');

        $admin = $this->createUser('admin', 'admin-user-pegawai-search@example.test', $unitA->id);
        $pegawaiActor = $this->createUser('pegawai', 'pegawai-search-actor@example.test', $unitA->id, 'Pegawai Search Actor');
        $unitAPegawai = $this->createUser('pegawai', 'pegawai.search.a@example.test', $unitA->id, 'Ayu Unit A');
        $unitBPegawai = $this->createUser('pegawai', 'pegawai.search.b@example.test', $unitB->id, 'Bima Unit B');

        $this->actingAs($admin)
            ->post('/manajemen_setup/data_user_pegawai/cariUserPegawai', ['cariUserPegawai' => 'Unit'])
            ->assertOk()
            ->assertSee($unitAPegawai->username)
            ->assertDontSee($unitBPegawai->username);

        $this->actingAs($pegawaiActor)
            ->post('/manajemen_setup/data_user_pegawai/cariUserPegawai', ['cariUserPegawai' => 'Ayu'])
            ->assertForbidden();
    }

    public function test_user_admin_search_form_uses_get_route_and_remains_superadmin_only(): void
    {
        $superadmin = $this->createUser('superadmin', 'superadmin-user-admin-search@example.test');
        $adminActor = $this->createUser('admin', 'admin-user-admin-search@example.test', $this->unit('Admin Search Unit')->id);
        $matchedAdmin = $this->createUser('admin', 'matched-admin@example.test', $adminActor->unit_kerja_id, 'Matched Admin');
        $otherAdmin = $this->createUser('admin', 'other-admin@example.test', $adminActor->unit_kerja_id, 'Other Admin');

        $this->actingAs($superadmin)
            ->get('/manajemen_setup/data_user_admin')
            ->assertOk()
            ->assertSee('action="/manajemen_setup/data_user_admin/cariUserAdmin"', false)
            ->assertSee('method="GET"', false)
            ->assertSee('Terapkan')
            ->assertSee('Reset');

        $this->actingAs($superadmin)
            ->get('/manajemen_setup/data_user_admin/cariUserAdmin?cariUserAdmin=Matched')
            ->assertOk()
            ->assertSee($matchedAdmin->username)
            ->assertDontSee($otherAdmin->username)
            ->assertSee('1 filter aktif');

        $this->actingAs($adminActor)
            ->get('/manajemen_setup/data_user_admin/cariUserAdmin?cariUserAdmin=Matched')
            ->assertForbidden();
    }

    private function createUser(string $role, string $email, ?int $unitKerjaId = null, ?string $name = null): User
    {
        return User::create([
            'username' => str_replace(['@', '.'], '-', $email),
            'name' => $name ?? ucfirst($role) . ' User',
            'email' => $email,
            'email_verified_at' => now(),
            'role' => $role,
            'unit_kerja_id' => $unitKerjaId,
            'password' => bcrypt('password'),
        ]);
    }

    private function unit(string $name): UnitKerja
    {
        return UnitKerja::create([
            'nama_unit' => $name,
            'alamat' => 'Jl. ' . $name,
        ]);
    }
}
