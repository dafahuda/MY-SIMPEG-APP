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

class DiklatCancelledPlanLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelled_plans_cannot_be_linked_to_realization(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawaiPair();
        $rencana = $this->createRencana($pegawai, '2026', 'Pelatihan Dibatalkan', 'cancelled');

        Storage::fake('public');

        $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', array_merge($this->payload($pegawai->id), [
                'rencana_diklat_id' => $rencana->id,
                'file_sertifikat_diklat' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
            ]))
            ->assertSessionHasErrors(['rencana_diklat_id']);

        $this->assertDatabaseMissing('tb_diklat', [
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
        ]);
        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
            'status' => 'cancelled',
        ]);
    }

    private function payload(int $pegawaiId): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'nama_diklat' => 'Diklat Cancelled',
            'jumlah_jam' => '40',
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-CANCEL-001',
            'tgl_sttpp' => '2026-05-09',
        ];
    }

    private function seedAdminAndPegawaiPair(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Cancelled',
            'alamat' => 'Jl. Cancelled No. 1',
        ]);

        $admin = User::create([
            'username' => 'admin-cancelled-link',
            'name' => 'Admin Cancelled Link',
            'email' => 'admin.cancelled.link@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiUser = User::create([
            'username' => 'pegawai-cancelled-link',
            'name' => 'Pegawai Cancelled Link',
            'email' => 'pegawai.cancelled.link@example.test',
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
            'nama' => 'Pegawai Cancelled Link',
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
            'email' => 'pegawai.cancelled.link@example.test',
            'email_gov' => 'pegawai.cancelled.link@gov.test',
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
}
