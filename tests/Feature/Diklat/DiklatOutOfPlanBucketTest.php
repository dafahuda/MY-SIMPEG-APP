<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatOutOfPlanBucketTest extends TestCase
{
    use RefreshDatabase;

    public function test_out_of_plan_realizations_are_rendered_in_their_own_bucket(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawai();

        $this->createRencana($pegawai, '2026', 'Rencana Terikat', 'planned');
        $this->createDiklat($pegawai, null, '2027', 'Diklat Bebas');

        $response = $this->actingAs($admin)->get(route('report.diklat_gap', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'pegawai_id' => $pegawai->id,
            'unit_kerja_id' => $pegawai->unit_kerja_id,
        ]));

        $response->assertOk()
            ->assertSeeHtml('data-testid="diklat-gap-section-out-of-plan"')
            ->assertSeeHtml('data-testid="diklat-gap-bucket-count-out-of-plan" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-row-out-of-plan-0"')
            ->assertSee('Diklat Bebas')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-out_of_plan" data-value="1"');
    }

    private function seedAdminAndPegawai(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Out Of Plan',
            'alamat' => 'Jl. Out Of Plan',
        ]);

        $admin = User::create([
            'username' => 'admin-out-of-plan',
            'name' => 'Admin Out Of Plan',
            'email' => 'admin.out.of.plan@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiUser = User::create([
            'username' => 'pegawai-out-of-plan',
            'name' => 'Pegawai Out Of Plan',
            'email' => 'pegawai.out.of.plan@example.test',
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
            'nama' => 'Pegawai Out Of Plan',
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
            'email' => 'pegawai.out.of.plan@example.test',
            'email_gov' => 'pegawai.out.of.plan@gov.test',
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

    private function createRencana(Pegawai $pegawai, string $tahun, string $nama, string $status): RencanaDiklat
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
            'status' => $status,
        ]);
    }

    private function createDiklat(Pegawai $pegawai, ?RencanaDiklat $rencana, string $tahun, string $nama): Diklat
    {
        return Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana?->id,
            'nama_diklat' => $nama,
            'jumlah_jam' => 24,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => $tahun,
            'no_sttpp' => 'STTPP-' . $nama,
            'tgl_sttpp' => $tahun . '-05-09',
            'file_sertifikat_diklat' => null,
        ]);
    }
}
