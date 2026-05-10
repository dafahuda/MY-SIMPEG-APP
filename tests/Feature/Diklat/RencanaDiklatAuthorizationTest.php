<?php

namespace Tests\Feature\Diklat;

use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RencanaDiklatAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_cannot_access_mutation_routes_even_directly(): void
    {
        [$pegawaiUser, $pegawai] = $this->createPegawaiUser('pegawai-direct', 'pegawai.direct@example.test', 'Pegawai Direct', 'Unit Direct');

        $rencana = $this->createRencana($pegawai, 'Rencana Direct');

        $this->actingAs($pegawaiUser);

        $this->get(route('rencana_diklat.create'))->assertForbidden();
        $this->post(route('rencana_diklat.store'), $this->payload($pegawai->id, 'draft', 'Rencana Baru'))->assertForbidden();
        $this->get(route('rencana_diklat.edit', $rencana))->assertForbidden();
        $this->put(route('rencana_diklat.update', $rencana), $this->payload($pegawai->id, 'planned', 'Rencana Edit'))->assertForbidden();
        $this->delete(route('rencana_diklat.destroy', $rencana))->assertForbidden();

        $this->get(route('rencana_diklat.index'))
            ->assertOk()
            ->assertSee('Rencana Direct')
            ->assertDontSeeHtml('data-testid="rencana-diklat-create-button"');
    }

    public function test_admin_is_scoped_to_own_unit_for_listing_and_mutations(): void
    {
        [$admin, $pegawaiUnitA, $pegawaiUnitB] = $this->createAdminAndCrossUnitUsers();

        $rencanaA = $this->createRencana($pegawaiUnitA, 'Rencana Unit A');
        $rencanaB = $this->createRencana($pegawaiUnitB, 'Rencana Unit B');

        $this->actingAs($admin);

        $this->get(route('rencana_diklat.index'))
            ->assertOk()
            ->assertSee('Rencana Unit A')
            ->assertDontSee('Rencana Unit B');

        $this->get(route('rencana_diklat.create'))
            ->assertOk()
            ->assertSee('Pegawai Unit A')
            ->assertDontSee('Pegawai Unit B');

        $this->post(route('rencana_diklat.store'), $this->payload($pegawaiUnitB->id, 'planned', 'Rencana Gagal'))
            ->assertSessionHasErrors('pegawai_id');

        $this->get(route('rencana_diklat.edit', $rencanaA))->assertOk();
        $this->get(route('rencana_diklat.edit', $rencanaB))->assertForbidden();
        $this->put(route('rencana_diklat.update', $rencanaB), $this->payload($pegawaiUnitB->id, 'planned', 'Rencana Gagal'))->assertForbidden();
        $this->delete(route('rencana_diklat.destroy', $rencanaB))->assertForbidden();
    }

    public function test_superadmin_can_manage_all_units_globally(): void
    {
        [$superadmin, $pegawaiUnitA, $pegawaiUnitB] = $this->createSuperadminAndTwoPegawai();

        $rencanaA = $this->createRencana($pegawaiUnitA, 'Rencana Global A');
        $rencanaB = $this->createRencana($pegawaiUnitB, 'Rencana Global B');

        $this->actingAs($superadmin);

        $this->get(route('rencana_diklat.index'))
            ->assertOk()
            ->assertSee('Rencana Global A')
            ->assertSee('Rencana Global B');

        $this->get(route('rencana_diklat.create'))
            ->assertOk()
            ->assertSee('Pegawai Global A')
            ->assertSee('Pegawai Global B');

        $this->post(route('rencana_diklat.store'), $this->payload($pegawaiUnitB->id, 'planned', 'Rencana Global Baru'))
            ->assertRedirect(route('rencana_diklat.index'));

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'pegawai_id' => $pegawaiUnitB->id,
            'nama_diklat_rencana' => 'Rencana Global Baru',
            'status' => 'planned',
        ]);

        $this->get(route('rencana_diklat.edit', $rencanaA))->assertOk();
        $this->put(route('rencana_diklat.update', $rencanaB), $this->payload($pegawaiUnitB->id, 'draft', 'Rencana Global B Revisi', 'draft'))
            ->assertRedirect(route('rencana_diklat.index'));

        $this->delete(route('rencana_diklat.destroy', $rencanaA))->assertRedirect(route('rencana_diklat.index'));
    }

    private function createAdminAndCrossUnitUsers(): array
    {
        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit A',
            'alamat' => 'Jl. A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit B',
            'alamat' => 'Jl. B',
        ]);

        $admin = User::create([
            'username' => 'admin-scope',
            'name' => 'Admin Scope',
            'email' => 'admin.scope@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitA->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiA = $this->createPegawaiUser('pegawai-a', 'pegawai.a@example.test', 'Pegawai Unit A', $unitA->id);
        $pegawaiB = $this->createPegawaiUser('pegawai-b', 'pegawai.b@example.test', 'Pegawai Unit B', $unitB->id);

        return [$admin, $pegawaiA[1], $pegawaiB[1]];
    }

    private function createSuperadminAndTwoPegawai(): array
    {
        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit Global A',
            'alamat' => 'Jl. Global A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit Global B',
            'alamat' => 'Jl. Global B',
        ]);

        $superadmin = User::create([
            'username' => 'superadmin-rencana',
            'name' => 'Superadmin Rencana',
            'email' => 'superadmin.rencana@example.test',
            'role' => 'superadmin',
            'unit_kerja_id' => null,
            'password' => bcrypt('password'),
        ]);

        $pegawaiA = $this->createPegawaiUser('pegawai-global-a', 'pegawai.globala@example.test', 'Pegawai Global A', $unitA->id);
        $pegawaiB = $this->createPegawaiUser('pegawai-global-b', 'pegawai.globalb@example.test', 'Pegawai Global B', $unitB->id);

        return [$superadmin, $pegawaiA[1], $pegawaiB[1]];
    }

    private function createPegawaiUser(string $username, string $email, string $nama, int|string $unitKerjaIdOrName): array
    {
        $unitKerjaId = $unitKerjaIdOrName;

        if (is_string($unitKerjaIdOrName)) {
            $unitKerjaId = UnitKerja::create([
                'nama_unit' => $unitKerjaIdOrName,
                'alamat' => 'Jl. ' . $unitKerjaIdOrName,
            ])->id;
        }

        $user = User::create([
            'username' => $username,
            'name' => $nama,
            'email' => $email,
            'role' => 'pegawai',
            'unit_kerja_id' => $unitKerjaId,
            'password' => bcrypt('password'),
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerjaId,
            'foto' => 'foto.jpg',
            'nip' => '198801012020011001',
            'nik' => '3276010101880001',
            'nama' => $nama,
            'gelar' => 'S.T.',
            'gelar_depan' => 'Ir.',
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1988-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Nikah',
            'alamat' => 'Jl. Contoh No. 1',
            'no_hp' => '081234567890',
            'email' => $email,
            'email_gov' => $email,
            'no_npwp' => '00.000.000.0-000.000',
            'no_bpjs' => '0000000000000001',
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-001',
            'no_sk_cpns' => 'SKCPNS-001',
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-001',
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'nilai_tpp' => 0,
        ]);

        return [$user, $pegawai];
    }

    private function createRencana(Pegawai $pegawai, string $nama): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => $nama,
            'target_kompetensi' => 'Kepemimpinan',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan jabatan',
            'catatan' => null,
            'status' => 'planned',
        ]);
    }

    private function payload(int $pegawaiId, string $status, string $nama, ?string $saveMode = null): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => $nama,
            'target_kompetensi' => 'Kepemimpinan',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan jabatan',
            'catatan' => 'Catatan tambahan',
            'status' => $status,
            'save_mode' => $saveMode ?? $status,
        ];
    }
}
