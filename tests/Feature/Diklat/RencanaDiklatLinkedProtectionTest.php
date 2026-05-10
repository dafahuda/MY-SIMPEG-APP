<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RencanaDiklatLinkedProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_linked_rencana_diklat_cannot_change_identity_or_be_deleted(): void
    {
        [$admin, $pegawai] = $this->createAdminAndPegawaiPair();

        $rencana = RencanaDiklat::create($this->payload($pegawai->id, 'Pelatihan Dasar'));

        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Pelatihan Dasar',
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-LINKED-001',
            'tgl_sttpp' => '2026-05-09',
            'file_sertifikat_diklat' => null,
        ]);

        $this->actingAs($admin)
            ->put(route('rencana_diklat.update', $rencana), array_merge($this->payload($pegawai->id, 'Pelatihan Dasar Revisi'), [
                'save_mode' => 'planned',
                'status' => 'planned',
            ]))
            ->assertSessionHas('error', 'Rencana diklat yang sudah terhubung tidak bisa mengubah identitasnya.');

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
            'nama_diklat_rencana' => 'Pelatihan Dasar',
        ]);

        $this->actingAs($admin)
            ->delete(route('rencana_diklat.destroy', $rencana))
            ->assertSessionHas('error', 'Rencana diklat yang sudah terhubung tidak bisa dihapus.');

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
        ]);
    }

    public function test_linked_rencana_diklat_keeps_realized_status_when_non_identity_fields_are_updated(): void
    {
        [$admin, $pegawai] = $this->createAdminAndPegawaiPair();

        $rencana = RencanaDiklat::create($this->payload($pegawai->id, 'Pelatihan Dasar'));

        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Pelatihan Dasar',
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-LINKED-002',
            'tgl_sttpp' => '2026-05-09',
            'file_sertifikat_diklat' => null,
        ]);

        $rencana->update(['status' => 'realized']);

        $this->actingAs($admin)
            ->put(route('rencana_diklat.update', $rencana), array_merge($this->payload($pegawai->id, 'Pelatihan Dasar'), [
                'target_jam' => 48,
                'catatan' => 'Catatan revisi non-identitas',
                'save_mode' => 'planned',
                'status' => 'planned',
            ]))
            ->assertRedirect(route('rencana_diklat.index'));

        $this->assertDatabaseHas('tb_rencana_diklat', [
            'id' => $rencana->id,
            'status' => 'realized',
            'target_jam' => 48,
            'catatan' => 'Catatan revisi non-identitas',
        ]);
    }

    private function payload(int $pegawaiId, string $nama): array
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
            'status' => 'planned',
        ];
    }

    private function createAdminAndPegawaiPair(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Linked Protection',
            'alamat' => 'Jl. Linked Protection No. 1',
        ]);

        $admin = User::create([
            'username' => 'admin-linked-protection',
            'name' => 'Admin Linked Protection',
            'email' => 'admin.linked.protection@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiUser = User::create([
            'username' => 'pegawai-linked-protection',
            'name' => 'Pegawai Linked Protection',
            'email' => 'pegawai.linked.protection@example.test',
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
            'nama' => 'Pegawai Linked Protection',
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
            'email' => 'pegawai.linked.protection@example.test',
            'email_gov' => 'pegawai.linked.protection@gov.test',
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
