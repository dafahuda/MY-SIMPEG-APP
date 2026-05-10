<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatGapRoleScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_escape_own_unit_scope(): void
    {
        [$admin, $pegawaiA, $pegawaiB] = $this->seedTwoUnitsWithAdmin();

        $this->createRencana($pegawaiA, '2026', 'Rencana Unit A');
        $this->createRencana($pegawaiB, '2026', 'Rencana Unit B');

        $response = $this->actingAs($admin)->get(route('report.diklat_gap', [
            'tahun_rencana' => '2026',
            'unit_kerja_id' => $pegawaiB->unit_kerja_id,
            'pegawai_id' => $pegawaiB->id,
        ]));

        $response->assertOk()
            ->assertSee('Rencana Unit A')
            ->assertDontSee('Rencana Unit B')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-planned" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-select" data-selected="' . $pegawaiA->unit_kerja_id . '"');
    }

    public function test_pegawai_is_forced_to_self_scope_only(): void
    {
        [$pegawaiA, $pegawaiB] = $this->seedTwoPegawaiAcrossUnits();

        $this->createRencana($pegawaiA, '2026', 'Rencana Pegawai A');
        $this->createRencana($pegawaiB, '2026', 'Rencana Pegawai B');

        $response = $this->actingAs($pegawaiB->user)->get(route('report.diklat_gap', [
            'tahun_rencana' => '2026',
            'unit_kerja_id' => $pegawaiA->unit_kerja_id,
            'pegawai_id' => $pegawaiA->id,
        ]));

        $response->assertOk()
            ->assertSee('Rencana Pegawai B')
            ->assertDontSee('Rencana Pegawai A')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-planned" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-pegawai-select" data-selected="' . $pegawaiB->id . '"')
            ->assertDontSeeHtml('data-testid="diklat-gap-unit-select"');
    }

    private function seedTwoUnitsWithAdmin(): array
    {
        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit A Scope',
            'alamat' => 'Jl. A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit B Scope',
            'alamat' => 'Jl. B',
        ]);

        $admin = User::create([
            'username' => 'admin-gap-scope',
            'name' => 'Admin Gap Scope',
            'email' => 'admin.gap.scope@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitA->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiA = $this->createPegawai('pegawai-gap-a', 'pegawai.gap.a@example.test', 'Pegawai Gap A', $unitA);
        $pegawaiB = $this->createPegawai('pegawai-gap-b', 'pegawai.gap.b@example.test', 'Pegawai Gap B', $unitB);

        return [$admin, $pegawaiA, $pegawaiB];
    }

    private function seedTwoPegawaiAcrossUnits(): array
    {
        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit Pegawai A',
            'alamat' => 'Jl. Pegawai A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit Pegawai B',
            'alamat' => 'Jl. Pegawai B',
        ]);

        $pegawaiA = $this->createPegawai('pegawai-self-a', 'pegawai.self.a@example.test', 'Pegawai Self A', $unitA);
        $pegawaiB = $this->createPegawai('pegawai-self-b', 'pegawai.self.b@example.test', 'Pegawai Self B', $unitB);

        return [$pegawaiA, $pegawaiB];
    }

    private function createPegawai(string $username, string $email, string $nama, UnitKerja $unitKerja): Pegawai
    {
        $user = User::create([
            'username' => $username,
            'name' => $nama,
            'email' => $email,
            'role' => 'pegawai',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        return Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
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
    }

    private function createRencana(Pegawai $pegawai, string $tahun, string $nama): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => $tahun,
            'nama_diklat_rencana' => $nama,
            'target_kompetensi' => 'Kompetensi',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan',
            'catatan' => null,
            'status' => 'planned',
        ]);
    }
}
