<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_only_eligible_plans_and_link_a_realization(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawaiPair();

        $eligiblePlan = $this->createRencana($pegawai, '2026', 'Pelatihan Kepemimpinan Dasar');
        $this->createRencana($pegawai, '2028', 'Pelatihan Tidak Eligible');

        $diklat = Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => null,
            'nama_diklat' => 'Diklat Realisasi',
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2027',
            'no_sttpp' => 'STTPP-LINK-001',
            'tgl_sttpp' => '2027-05-09',
            'file_sertifikat_diklat' => '/storage/document/diklat-link.pdf',
        ]);

        $this->actingAs($admin)
            ->get('/kepegawaian/diklat/view_form_edit_diklat/' . $diklat->id)
            ->assertOk()
            ->assertSeeHtml('data-testid="diklat-rencana-select"')
            ->assertSee('Pelatihan Kepemimpinan Dasar')
            ->assertDontSee('Pelatihan Tidak Eligible');

        $this->actingAs($admin)
            ->put('/kepegawaian/diklat/edit_diklat/' . $diklat->id, array_merge($this->payload($pegawai->id, '2027'), [
                'rencana_diklat_id' => $eligiblePlan->id,
            ]))
            ->assertRedirect('/kepegawaian/diklat');

        $this->assertDatabaseHas('tb_diklat', [
            'id' => $diklat->id,
            'rencana_diklat_id' => $eligiblePlan->id,
        ]);
        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $eligiblePlan->id,
            'status' => 'realized',
        ]);

        $this->actingAs($admin)
            ->get('/kepegawaian/diklat')
            ->assertOk()
            ->assertSeeHtml('data-testid="diklat-link-status-badge"')
            ->assertSee('Linked');
    }

    private function payload(int $pegawaiId, string $tahun): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'nama_diklat' => 'Diklat Realisasi',
            'jumlah_jam' => '40',
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => $tahun,
            'no_sttpp' => 'STTPP-LINK-001',
            'tgl_sttpp' => '2027-05-09',
        ];
    }

    private function seedAdminAndPegawaiPair(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Linking',
            'alamat' => 'Jl. Linking No. 1',
        ]);

        $admin = User::create([
            'username' => 'admin-linking',
            'name' => 'Admin Linking',
            'email' => 'admin.linking@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiUser = User::create([
            'username' => 'pegawai-linking',
            'name' => 'Pegawai Linking',
            'email' => 'pegawai.linking@example.test',
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
            'nama' => 'Pegawai Linking',
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
            'email' => 'pegawai.linking@example.test',
            'email_gov' => 'pegawai.linking@gov.test',
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
