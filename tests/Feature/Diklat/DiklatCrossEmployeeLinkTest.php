<?php

namespace Tests\Feature\Diklat;

use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiklatCrossEmployeeLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_cross_employee_links_are_rejected_before_persistence(): void
    {
        [$admin, $pegawaiA, $pegawaiB] = $this->seedAdminAndTwoPegawai();
        $rencana = $this->createRencana($pegawaiA, '2026', 'Pelatihan Kepemimpinan Dasar');

        Storage::fake('public');

        $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', array_merge($this->payload($pegawaiB->id), [
                'rencana_diklat_id' => $rencana->id,
                'file_sertifikat_diklat' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
            ]))
            ->assertSessionHasErrors(['rencana_diklat_id']);

        $this->assertDatabaseMissing('tb_diklat', [
            'pegawai_id' => $pegawaiB->id,
            'rencana_diklat_id' => $rencana->id,
        ]);
        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
            'status' => 'planned',
        ]);
    }

    private function payload(int $pegawaiId): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'nama_diklat' => 'Diklat Cross Employee',
            'jumlah_jam' => '40',
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-CROSS-001',
            'tgl_sttpp' => '2026-05-09',
        ];
    }

    private function seedAdminAndTwoPegawai(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Cross Employee',
            'alamat' => 'Jl. Cross Employee No. 1',
        ]);

        $admin = User::create([
            'username' => 'admin-cross-link',
            'name' => 'Admin Cross Link',
            'email' => 'admin.cross.link@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiA = $this->createPegawai(
            'pegawai-cross-a',
            'pegawai.cross.a@example.test',
            'Pegawai Cross A',
            $unitKerja
        );

        $pegawaiB = $this->createPegawai(
            'pegawai-cross-b',
            'pegawai.cross.b@example.test',
            'Pegawai Cross B',
            $unitKerja
        );

        return [$admin, $pegawaiA, $pegawaiB];
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
