<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiklatUnlinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_unlinking_a_realization_restores_the_plan_status(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawaiPair();
        $rencana = $this->createRencana($pegawai, '2026', 'Pelatihan Kepemimpinan Dasar');

        $diklat = $this->storeLinkedDiklat($admin, $pegawai, $rencana);

        $this->actingAs($admin)
            ->put('/kepegawaian/diklat/edit_diklat/' . $diklat->id, array_merge($this->payload($pegawai->id), [
                'rencana_diklat_id' => null,
            ]))
            ->assertRedirect('/kepegawaian/diklat');

        $this->assertDatabaseHas('tb_diklat', [
            'id' => $diklat->id,
            'rencana_diklat_id' => null,
        ]);
        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
            'status' => 'planned',
        ]);
    }

    public function test_deleting_a_linked_realization_restores_the_plan_status(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawaiPair();
        $rencana = $this->createRencana($pegawai, '2026', 'Pelatihan Kepemimpinan Dasar');

        $diklat = $this->storeLinkedDiklat($admin, $pegawai, $rencana);

        $this->actingAs($admin)
            ->delete('/kepegawaian/diklat/delete_data_diklat/' . $diklat->id)
            ->assertRedirect('/kepegawaian/diklat');

        $this->assertDatabaseMissing('tb_diklat', [
            'id' => $diklat->id,
        ]);
        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
            'status' => 'planned',
        ]);
    }

    private function storeLinkedDiklat(User $admin, Pegawai $pegawai, RencanaDiklat $rencana): Diklat
    {
        Storage::fake('public');

        $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', array_merge($this->payload($pegawai->id), [
                'rencana_diklat_id' => $rencana->id,
                'file_sertifikat_diklat' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
            ]))
            ->assertRedirect('/kepegawaian/diklat');

        return Diklat::query()->where('rencana_diklat_id', $rencana->id)->firstOrFail();
    }

    private function payload(int $pegawaiId): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'nama_diklat' => 'Diklat Realisasi',
            'jumlah_jam' => '40',
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-UNLINK-001',
            'tgl_sttpp' => '2026-05-09',
        ];
    }

    private function seedAdminAndPegawaiPair(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Unlinking',
            'alamat' => 'Jl. Unlinking No. 1',
        ]);

        $admin = User::create([
            'username' => 'admin-unlinking',
            'name' => 'Admin Unlinking',
            'email' => 'admin.unlinking@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiUser = User::create([
            'username' => 'pegawai-unlinking',
            'name' => 'Pegawai Unlinking',
            'email' => 'pegawai.unlinking@example.test',
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
            'nama' => 'Pegawai Unlinking',
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
            'email' => 'pegawai.unlinking@example.test',
            'email_gov' => 'pegawai.unlinking@gov.test',
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
