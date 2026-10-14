<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\RiwayatKeluargaAnak;
use App\Models\UnitKerja;
use App\Models\User;
use App\Support\Access;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AccessFacadeTest extends TestCase
{
    use RefreshDatabase;

    private function employee(UnitKerja $unit, string $name): Pegawai
    {
        $user = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);

        return Pegawai::factory()->create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unit->id,
            'nama' => $name,
        ]);
    }

    public function test_scope_to_self_only_returns_linked_records_and_none_without_a_link(): void
    {
        $unit = UnitKerja::factory()->create();
        $own = $this->employee($unit, 'Own');
        $foreign = $this->employee($unit, 'Foreign');
        $ownRecord = RiwayatKeluargaAnak::factory()->create(['pegawai_id' => $own->id]);
        RiwayatKeluargaAnak::factory()->create(['pegawai_id' => $foreign->id]);

        $this->actingAs($own->user);
        $this->assertEquals([$ownRecord->id], Access::scopeToSelf(RiwayatKeluargaAnak::query())->pluck('id')->all());

        $this->actingAs(User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]));
        $this->assertCount(0, Access::scopeToSelf(RiwayatKeluargaAnak::query())->get());
    }

    public function test_scope_to_unit_only_returns_admin_unit_records(): void
    {
        $unit = UnitKerja::factory()->create();
        $otherUnit = UnitKerja::factory()->create();
        $own = $this->employee($unit, 'Own');
        $foreign = $this->employee($otherUnit, 'Foreign');
        $ownRecord = RiwayatKeluargaAnak::factory()->create(['pegawai_id' => $own->id]);
        RiwayatKeluargaAnak::factory()->create(['pegawai_id' => $foreign->id]);

        $this->actingAs(User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]));
        $this->assertEquals([$ownRecord->id], Access::scopeToUnit(RiwayatKeluargaAnak::query())->pluck('id')->all());
    }

    public function test_pegawai_options_follow_role_and_are_ordered_by_name(): void
    {
        $unit = UnitKerja::factory()->create();
        $otherUnit = UnitKerja::factory()->create();
        $adminEmployee = $this->employee($unit, 'B');
        $own = $this->employee($unit, 'A');
        $outside = $this->employee($otherUnit, 'C');

        $this->actingAs(User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]));
        $this->assertEquals([$own->id, $adminEmployee->id], Access::pegawaiOptions()->pluck('id')->all());

        $this->actingAs($own->user);
        $this->assertEquals([$own->id], Access::pegawaiOptions()->pluck('id')->all());

        $this->actingAs(User::factory()->create(['role' => 'superadmin']));
        $this->assertEquals([$own->id, $adminEmployee->id, $outside->id], Access::pegawaiOptions()->pluck('id')->all());
    }

    public function test_ensure_own_record_rejects_foreign_and_passes_own_admin_and_superadmin(): void
    {
        $unit = UnitKerja::factory()->create();
        $own = $this->employee($unit, 'Own');
        $foreign = $this->employee($unit, 'Foreign');
        $ownRecord = RiwayatKeluargaAnak::factory()->create(['pegawai_id' => $own->id]);
        $foreignRecord = RiwayatKeluargaAnak::factory()->create(['pegawai_id' => $foreign->id]);

        $this->actingAs($own->user);
        Access::ensureOwnRecord($ownRecord);
        $this->assertForbidden(fn () => Access::ensureOwnRecord($foreignRecord));

        foreach (['admin', 'superadmin'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            Access::ensureOwnRecord($foreignRecord);
        }
        $this->assertTrue(true);
    }

    public function test_ensure_in_unit_rejects_foreign_unit(): void
    {
        $unit = UnitKerja::factory()->create();
        $own = $this->employee($unit, 'Own');
        $outside = $this->employee(UnitKerja::factory()->create(), 'Outside');

        $this->actingAs(User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]));
        Access::ensureInUnit($own);
        $this->assertForbidden(fn () => Access::ensureInUnit($outside));
    }

    public function test_abort_unless_role_rejects_pegawai_on_admin_superadmin_gate(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'pegawai']));
        $this->assertForbidden(fn () => Access::abortUnlessRole('admin', 'superadmin'));
    }

    public function test_role_middleware_allows_listed_roles_and_blocks_others(): void
    {
        $unit = UnitKerja::factory()->create();

        $this->actingAs(User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]));
        $this->get('/rekapitulasi/agama')->assertOk();

        $this->actingAs(User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]));
        $this->get('/rekapitulasi/agama')->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'superadmin']));
        $this->get('/manajemen_setup/data_user_admin')->assertOk();

        $this->actingAs(User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]));
        $this->get('/manajemen_setup/data_user_admin')->assertForbidden();
    }

    private function assertForbidden(callable $callback): void
    {
        try {
            $callback();
            $this->fail('Expected a 403 response.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
            $this->assertSame('Akses ditolak', $exception->getMessage());
        }
    }
}
