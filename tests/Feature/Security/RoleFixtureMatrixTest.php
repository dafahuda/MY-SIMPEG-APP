<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\Feature\Security\Support\RoleMatrixFixtures;
use Tests\TestCase;

class RoleFixtureMatrixTest extends TestCase
{
    use RoleMatrixFixtures;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('superadmin', fn (User $user): bool => $user->role === 'superadmin');
    }

    public function test_role_matrix_fixtures_cover_superadmin_admin_and_pegawai(): void
    {
        $unit = $this->createUnitKerja('Unit Fixture Matrix');

        $superadmin = $this->createRoleUser('superadmin', 'fixture-superadmin', 'fixture.superadmin@example.test');
        $admin = $this->createRoleUser('admin', 'fixture-admin', 'fixture.admin@example.test', $unit->id);
        [$pegawai, $pegawaiBiodata] = $this->createPegawaiUser('fixture-pegawai', 'fixture.pegawai@example.test', 'Fixture Pegawai', $unit->id);

        $this->assertSame('superadmin', $superadmin->role);
        $this->assertSame('admin', $admin->role);
        $this->assertSame('pegawai', $pegawai->role);
        $this->assertNotNull($pegawaiBiodata->id);
        $this->assertSame($pegawai->id, $pegawaiBiodata->user_id);

        $this->actingAs($superadmin)->get('/backup_data')->assertOk();
        $this->actingAs($admin)->get('/backup_data')->assertForbidden();
        $this->actingAs($pegawai)->get('/backup_data')->assertForbidden();
    }
}
