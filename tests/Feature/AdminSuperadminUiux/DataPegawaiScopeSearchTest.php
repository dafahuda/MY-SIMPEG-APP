<?php

namespace Tests\Feature\AdminSuperadminUiux;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataPegawaiScopeSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_unit_a_cannot_see_unit_b_employee_on_index_or_search(): void
    {
        [$unitA, $pegawaiA] = $this->seedPegawai('A', 'Ayu Admin Scope');
        [, $pegawaiB] = $this->seedPegawai('B', 'Bima Other Unit');
        $admin = $this->createUser('admin-a', 'admin.a@example.test', 'Admin A', 'admin', $unitA->id);

        $this->actingAs($admin)
            ->get('/data_pegawai/pegawai')
            ->assertOk()
            ->assertSee($pegawaiA->nama)
            ->assertDontSee($pegawaiB->nama);

        $this->actingAs($admin)
            ->post('/data_pegawai/cariPegawai', ['cariPegawai' => 'Bima'])
            ->assertOk()
            ->assertDontSee($pegawaiB->nama);
    }

    public function test_admin_unit_a_cannot_open_unit_b_employee_edit_form(): void
    {
        [$unitA] = $this->seedPegawai('A', 'Ayu Admin Scope');
        [, $pegawaiB] = $this->seedPegawai('B', 'Bima Other Unit');
        $admin = $this->createUser('admin-a', 'admin.a@example.test', 'Admin A', 'admin', $unitA->id);

        $this->actingAs($admin)
            ->get('/data_pegawai/view_form_edit_data_pegawai/' . $pegawaiB->id)
            ->assertForbidden();
    }

    public function test_superadmin_can_see_global_data_pegawai_records(): void
    {
        [, $pegawaiA] = $this->seedPegawai('A', 'Ayu Global Scope');
        [, $pegawaiB] = $this->seedPegawai('B', 'Bima Global Scope');
        $superadmin = $this->createUser('superadmin', 'superadmin@example.test', 'Super Admin', 'superadmin');

        $this->actingAs($superadmin)
            ->get('/data_pegawai/pegawai')
            ->assertOk()
            ->assertSee($pegawaiA->nama)
            ->assertSee($pegawaiB->nama);
    }

    public function test_search_form_uses_existing_post_route_and_get_is_not_accepted(): void
    {
        $superadmin = $this->createUser('superadmin', 'superadmin@example.test', 'Super Admin', 'superadmin');

        $this->actingAs($superadmin)
            ->get('/data_pegawai/pegawai')
            ->assertOk()
            ->assertSee('action="/data_pegawai/cariPegawai"', false)
            ->assertSee('method="POST"', false);

        $this->actingAs($superadmin)
            ->get('/data_pegawai/cariPegawai?cariPegawai=Ayu')
            ->assertMethodNotAllowed();
    }

    public function test_empty_search_result_renders_empty_state_message(): void
    {
        [$unitA] = $this->seedPegawai('A', 'Ayu Admin Scope');
        $admin = $this->createUser('admin-a', 'admin.a@example.test', 'Admin A', 'admin', $unitA->id);

        $this->actingAs($admin)
            ->post('/data_pegawai/cariPegawai', ['cariPegawai' => 'TidakAdaPegawaiIni'])
            ->assertOk()
            ->assertSee('Tidak ada data pegawai');
    }

    private function seedPegawai(string $suffix, string $nama): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit ' . $suffix,
            'alamat' => 'Jl. ' . $suffix,
        ]);

        $pegawai = $this->createPegawai(
            'pegawai-' . strtolower($suffix),
            'pegawai.' . strtolower($suffix) . '@example.test',
            $nama,
            $unitKerja,
            $suffix
        );

        return [$unitKerja, $pegawai];
    }

    private function createUser(string $username, string $email, string $name, string $role, ?int $unitKerjaId = null): User
    {
        return User::create([
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'unit_kerja_id' => $unitKerjaId,
            'password' => bcrypt('password'),
        ]);
    }

    private function createPegawai(string $username, string $email, string $nama, UnitKerja $unitKerja, string $suffix): Pegawai
    {
        $user = $this->createUser($username, $email, $nama, 'pegawai', $unitKerja->id);
        $digits = $suffix === 'A' ? '001' : '002';

        return Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto-' . strtolower($suffix) . '.jpg',
            'nip' => '198801012020011' . $digits,
            'nik' => '3276010101880' . $digits,
            'nama' => $nama,
            'gelar' => 'S.T.',
            'gelar_depan' => 'Ir.',
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1988-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Nikah',
            'alamat' => 'Jl. Contoh No. ' . $suffix,
            'no_hp' => '08123456789' . ($suffix === 'A' ? '0' : '1'),
            'email' => $email,
            'email_gov' => $email,
            'no_npwp' => '00.000.000.0-000.' . $digits,
            'no_bpjs' => '0000000000000' . $digits,
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-' . $suffix,
            'no_sk_cpns' => 'SKCPNS-' . $suffix,
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-' . $suffix,
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'nilai_tpp' => 0,
        ]);
    }
}
