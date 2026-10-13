<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PegawaiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_view_another_employee_edit_form(): void
    {
        [$andiUser, , $pegawaiLain] = $this->seedPegawaiFixture();

        $this->actingAs($andiUser)
            ->get('/data_pegawai/view_form_edit_data_pegawai/' . $pegawaiLain->id)
            ->assertForbidden();
    }

    public function test_employee_cannot_view_another_employee_pii_detail(): void
    {
        [$andiUser, , $pegawaiLain] = $this->seedPegawaiFixture();

        $this->actingAs($andiUser)
            ->get('/data_pegawai/detail_data_pegawai/' . $pegawaiLain->id)
            ->assertForbidden();
    }

    public function test_employee_cannot_update_another_employee_record(): void
    {
        [$andiUser, $andiPegawai, $pegawaiLain] = $this->seedPegawaiFixture();

        $this->actingAs($andiUser)
            ->put('/data_pegawai/ubah_data_pegawai/' . $pegawaiLain->id, $this->payload($andiPegawai))
            ->assertForbidden();
    }

    public function test_employee_cannot_access_employee_user_management_pages(): void
    {
        [$andiUser] = $this->seedPegawaiFixture();

        $this->actingAs($andiUser)
            ->get('/manajemen_setup/data_user_pegawai')
            ->assertForbidden();

        $this->actingAs($andiUser)
            ->get('/manajemen_setup/view_form_tambah_user_pegawai')
            ->assertForbidden();

        $this->actingAs($andiUser)
            ->get('/manajemen_setup/view_form_edit_user_pegawai/' . $andiUser->id)
            ->assertForbidden();

        $this->actingAs($andiUser)
            ->post('/manajemen_setup/tambah_user_pegawai', [
                'username' => 'employee.new',
                'name' => 'New Employee',
                'email' => 'employee.new@example.test',
                'password' => 'password',
            ])
            ->assertForbidden();

        $this->actingAs($andiUser)
            ->post('/manajemen_setup/data_user_pegawai/cariUserPegawai', [
                'cariUserPegawai' => 'pegawai',
            ])
            ->assertForbidden();
    }

    public function test_employee_cannot_access_employee_creation_pages(): void
    {
        [$andiUser] = $this->seedPegawaiFixture();

        $this->actingAs($andiUser)
            ->get('/data_pegawai/view_form_tambah_data_pegawai')
            ->assertForbidden();

        $this->actingAs($andiUser)
            ->post('/data_pegawai/tambah_data_pegawai', [])
            ->assertForbidden();
    }

    public function test_admin_and_superadmin_can_still_access_management_pages(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $pegawaiUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $pegawaiUser->id,
            'unit_kerja_id' => $unitKerja->id,
        ]);

        $this->actingAs($admin)
            ->get('/data_pegawai/view_form_tambah_data_pegawai')
            ->assertOk();
        $this->actingAs($admin)
            ->get('/data_pegawai/view_form_edit_data_pegawai/' . $pegawai->id)
            ->assertOk();
        $this->actingAs($admin)
            ->get('/manajemen_setup/data_user_pegawai')
            ->assertOk();
        $this->actingAs($admin)
            ->post('/data_pegawai/tambah_data_pegawai', [])
            ->assertSessionHasErrors();

        $this->actingAs($superadmin)
            ->get('/data_pegawai/view_form_tambah_data_pegawai')
            ->assertOk();
        $this->actingAs($superadmin)
            ->get('/data_pegawai/view_form_edit_data_pegawai/' . $pegawai->id)
            ->assertOk();
        $this->actingAs($superadmin)
            ->get('/manajemen_setup/data_user_pegawai')
            ->assertOk();
    }

    private function seedPegawaiFixture(): array
    {
        $unitKerja = UnitKerja::factory()->create();
        $andiUser = User::factory()->create([
            'username' => 'pegawai.andi',
            'name' => 'Employee Andi',
            'email' => 'pegawai.andi@example.test',
            'role' => 'pegawai',
            'unit_kerja_id' => $unitKerja->id,
        ]);
        $andiPegawai = Pegawai::factory()->create([
            'user_id' => $andiUser->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Employee Andi',
        ]);

        $userLain = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $pegawaiLain = Pegawai::factory()->create([
            'user_id' => $userLain->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Other Employee',
            'nik' => '3201010101010001',
        ]);

        return [$andiUser, $andiPegawai, $pegawaiLain];
    }

    private function payload(Pegawai $pegawai): array
    {
        return [
            'user_id' => $pegawai->user_id,
            'nip' => $pegawai->nip,
            'nama' => 'Unauthorized Change Attempt',
            'unit_kerja_id' => $pegawai->unit_kerja_id,
            'gelar' => $pegawai->gelar,
            'gelar_depan' => $pegawai->gelar_depan,
            'tmpt_lahir' => $pegawai->tmpt_lahir,
            'tgl_lahir' => $pegawai->tgl_lahir,
            'jenis_kelamin' => $pegawai->jenis_kelamin,
            'agama' => $pegawai->agama,
            'golongan_darah' => $pegawai->golongan_darah,
            'status_pernikahan' => $pegawai->status_pernikahan,
            'nik' => $pegawai->nik,
            'alamat' => $pegawai->alamat,
            'no_hp' => $pegawai->no_hp,
            'email' => $pegawai->email,
            'email_gov' => $pegawai->email_gov,
            'no_npwp' => $pegawai->no_npwp,
            'no_bpjs' => $pegawai->no_bpjs,
            'status_kepegawaian' => $pegawai->status_kepegawaian,
            'karpeg' => $pegawai->karpeg,
            'no_sk_cpns' => $pegawai->no_sk_cpns,
            'tmt_cpns' => $pegawai->tmt_cpns,
            'no_sk_pns' => $pegawai->no_sk_pns,
            'tmt_pns' => $pegawai->tmt_pns,
            'gol_awal' => $pegawai->gol_awal,
            'nilai_tpp' => $pegawai->nilai_tpp,
        ];
    }
}
