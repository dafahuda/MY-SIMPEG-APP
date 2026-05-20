<?php

namespace Tests\Feature\Diklat;

use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RencanaDiklatCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_search_update_and_delete_rencana_diklat(): void
    {
        [$admin, $pegawai] = $this->createAdminAndPegawaiPair();

        $this->actingAs($admin);

        $this->get(route('rencana_diklat.create'))
            ->assertOk()
            ->assertSeeHtml('data-testid="rencana-diklat-form"');

        $draftPayload = $this->payload($pegawai->id, 'draft', 'Pelatihan Dasar Draft');
        $this->post(route('rencana_diklat.store'), $draftPayload)
            ->assertRedirect(route('rencana_diklat.index'));

        $plannedPayload = $this->payload($pegawai->id, 'planned', 'Pelatihan Dasar Planned');
        $this->post(route('rencana_diklat.store'), $plannedPayload)
            ->assertRedirect(route('rencana_diklat.index'));

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'pegawai_id' => $pegawai->id,
            'nama_diklat_rencana' => 'Pelatihan Dasar Draft',
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('tb_rencana_diklat', [
            'pegawai_id' => $pegawai->id,
            'nama_diklat_rencana' => 'Pelatihan Dasar Planned',
            'status' => 'planned',
        ]);

        $indexResponse = $this->get(route('rencana_diklat.index'));
        $indexResponse->assertOk()
            ->assertSeeHtml('data-testid="rencana-diklat-table"')
            ->assertSee('Pelatihan Dasar Draft')
            ->assertSee('Pelatihan Dasar Planned');

        $this->post(route('rencana_diklat.search'), ['search' => 'Planned'])
            ->assertRedirect(route('rencana_diklat.index', ['search' => 'Planned']));

        $this->get(route('rencana_diklat.index', ['search' => 'Planned']))
            ->assertOk()
            ->assertSee('Pelatihan Dasar Planned')
            ->assertDontSee('Pelatihan Dasar Draft');

        $rencana = RencanaDiklat::where('nama_diklat_rencana', 'Pelatihan Dasar Planned')->firstOrFail();

        $this->get(route('rencana_diklat.edit', $rencana))
            ->assertOk()
            ->assertSeeHtml('data-testid="rencana-diklat-form"');

        $this->put(route('rencana_diklat.update', $rencana), array_merge($this->payload($pegawai->id, 'draft', 'Pelatihan Dasar Revisi'), [
            'save_mode' => 'draft',
            'status' => 'draft',
        ]))->assertRedirect(route('rencana_diklat.index'));

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
            'nama_diklat_rencana' => 'Pelatihan Dasar Revisi',
            'status' => 'draft',
        ]);

        $this->delete(route('rencana_diklat.destroy', $rencana))
            ->assertRedirect(route('rencana_diklat.index'));

        $this->assertDatabaseMissing('tb_rencana_diklat', ['id' => $rencana->id]);
    }

    public function test_save_mode_controls_draft_and_planned_statuses(): void
    {
        [$admin, $pegawai] = $this->createAdminAndPegawaiPair();

        $this->actingAs($admin);

        $this->post(route('rencana_diklat.store'), $this->payload($pegawai->id, 'planned', 'Rencana Draft Button', 'draft'))
            ->assertRedirect(route('rencana_diklat.index'));

        $this->post(route('rencana_diklat.store'), $this->payload($pegawai->id, 'draft', 'Rencana Final Button', 'planned'))
            ->assertRedirect(route('rencana_diklat.index'));

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'nama_diklat_rencana' => 'Rencana Draft Button',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'nama_diklat_rencana' => 'Rencana Final Button',
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

    private function createAdminAndPegawaiPair(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Pengembangan SDM',
            'alamat' => 'Jl. Merdeka No. 1',
        ]);

        $admin = User::create([
            'username' => 'admin-rencana',
            'name' => 'Admin Rencana',
            'email' => 'admin.rencana@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiUser = User::create([
            'username' => 'pegawai-rencana',
            'name' => 'Pegawai Rencana',
            'email' => 'pegawai.rencana@example.test',
            'role' => 'pegawai',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $pegawaiUser->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto.jpg',
            'nip' => '198801012020011001',
            'nik' => '3276010101880001',
            'nama' => 'Pegawai Rencana',
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
            'email' => 'pegawai.rencana@example.test',
            'email_gov' => 'pegawai.rencana@gov.test',
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

        return [$admin, $pegawai];
    }
}
