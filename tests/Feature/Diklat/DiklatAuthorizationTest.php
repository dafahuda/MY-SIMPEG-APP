<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiklatAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_only_sees_own_diklat_and_cannot_mutate_routes(): void
    {
        [$pegawaiUser, $pegawai, $ownDiklat, $otherDiklat] = $this->seedPegawaiVisibilityFixture();

        $this->actingAs($pegawaiUser)
            ->get('/kepegawaian/diklat')
            ->assertOk()
            ->assertSee('Diklat Milik Sendiri')
            ->assertDontSee('Diklat Unit Lain')
            ->assertDontSee('/kepegawaian/diklat/view_form_tambah_diklat')
            ->assertDontSee('/kepegawaian/diklat/view_form_edit_diklat/' . $ownDiklat->id)
            ->assertDontSee('/kepegawaian/diklat/delete_data_diklat/' . $ownDiklat->id);

        Storage::fake('public');

        $this->actingAs($pegawaiUser)->get('/kepegawaian/diklat/view_form_tambah_diklat')->assertForbidden();
        $this->actingAs($pegawaiUser)->get('/kepegawaian/diklat/view_form_edit_diklat/' . $ownDiklat->id)->assertForbidden();
        $this->actingAs($pegawaiUser)->post('/kepegawaian/diklat/tambah_diklat', array_merge($this->payload($pegawai->id), [
            'file_sertifikat_diklat' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
        ]))->assertForbidden();
        $this->actingAs($pegawaiUser)->put('/kepegawaian/diklat/edit_diklat/' . $ownDiklat->id, $this->payload($pegawai->id))->assertForbidden();
        $this->actingAs($pegawaiUser)->delete('/kepegawaian/diklat/delete_data_diklat/' . $ownDiklat->id)->assertForbidden();

        $this->actingAs($pegawaiUser)->get('/kepegawaian/diklat/download_sertifikat_diklat/' . $ownDiklat->id)->assertNotFound();
        $this->actingAs($pegawaiUser)->get('/kepegawaian/diklat/download_sertifikat_diklat/' . $otherDiklat->id)->assertForbidden();
    }

    public function test_admin_is_scoped_to_own_unit_for_diklat_mutations(): void
    {
        [$admin, $pegawaiUnitA, $diklatUnitA, $pegawaiUnitB, $diklatUnitB] = $this->seedAdminCrossUnitFixture();

        $this->actingAs($admin)
            ->get('/kepegawaian/diklat')
            ->assertOk()
            ->assertSee('Diklat Unit A')
            ->assertDontSee('Diklat Unit B');

        $this->actingAs($admin)->get('/kepegawaian/diklat/view_form_edit_diklat/' . $diklatUnitA->id)->assertOk();
        $this->actingAs($admin)->get('/kepegawaian/diklat/view_form_edit_diklat/' . $diklatUnitB->id)->assertForbidden();

        Storage::fake('public');

        $this->actingAs($admin)->post('/kepegawaian/diklat/tambah_diklat', array_merge($this->payload($pegawaiUnitB->id), [
            'file_sertifikat_diklat' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('pegawai_id');

        $this->actingAs($admin)->put('/kepegawaian/diklat/edit_diklat/' . $diklatUnitB->id, $this->payload($pegawaiUnitB->id))->assertForbidden();
        $this->actingAs($admin)->delete('/kepegawaian/diklat/delete_data_diklat/' . $diklatUnitB->id)->assertForbidden();
        $this->actingAs($admin)->get('/kepegawaian/diklat/download_sertifikat_diklat/' . $diklatUnitB->id)->assertForbidden();
    }

    private function seedPegawaiVisibilityFixture(): array
    {
        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit Pegawai A',
            'alamat' => 'Jl. Pegawai A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit Pegawai B',
            'alamat' => 'Jl. Pegawai B',
        ]);

        [$pegawaiUser, $pegawai] = $this->createPegawaiUser('pegawai-own', 'pegawai.own@example.test', 'Pegawai Own', $unitA);
        [, $pegawaiLain] = $this->createPegawaiUser('pegawai-other', 'pegawai.other@example.test', 'Pegawai Other', $unitB);

        $ownDiklat = $this->createDiklat($pegawai, 'Diklat Milik Sendiri');
        $otherDiklat = $this->createDiklat($pegawaiLain, 'Diklat Unit Lain');

        return [$pegawaiUser, $pegawai, $ownDiklat, $otherDiklat];
    }

    private function seedAdminCrossUnitFixture(): array
    {
        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit Admin A',
            'alamat' => 'Jl. Admin A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit Admin B',
            'alamat' => 'Jl. Admin B',
        ]);

        $admin = User::create([
            'username' => 'admin-diklat-scope',
            'name' => 'Admin Diklat Scope',
            'email' => 'admin.diklat.scope@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitA->id,
            'password' => bcrypt('password'),
        ]);

        [, $pegawaiUnitA] = $this->createPegawaiUser('pegawai-unit-a', 'pegawai.unita@example.test', 'Pegawai Unit A', $unitA);
        [, $pegawaiUnitB] = $this->createPegawaiUser('pegawai-unit-b', 'pegawai.unitb@example.test', 'Pegawai Unit B', $unitB);

        $diklatUnitA = $this->createDiklat($pegawaiUnitA, 'Diklat Unit A');
        $diklatUnitB = $this->createDiklat($pegawaiUnitB, 'Diklat Unit B');

        return [$admin, $pegawaiUnitA, $diklatUnitA, $pegawaiUnitB, $diklatUnitB];
    }

    private function createPegawaiUser(string $username, string $email, string $nama, UnitKerja $unitKerja): array
    {
        $user = User::create([
            'username' => $username,
            'name' => $nama,
            'email' => $email,
            'role' => 'pegawai',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawai = Pegawai::create([
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

        return [$user, $pegawai];
    }

    private function createDiklat(Pegawai $pegawai, string $nama): Diklat
    {
        return Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => null,
            'nama_diklat' => $nama,
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-' . md5($nama),
            'tgl_sttpp' => '2026-05-09',
            'file_sertifikat_diklat' => '/storage/document/' . md5($nama) . '.pdf',
        ]);
    }

    private function payload(int $pegawaiId): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'nama_diklat' => 'Diklat Baru',
            'jumlah_jam' => '40',
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-NEW-001',
            'tgl_sttpp' => '2026-05-09',
        ];
    }
}
