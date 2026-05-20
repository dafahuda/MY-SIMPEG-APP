<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RencanaDiklatRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationships_are_consistent_across_pegawai_rencana_and_diklat(): void
    {
        $pegawai = $this->createPegawai('pegawai-relasi', 'pegawai.relasi@example.test', 'Pegawai Relasi', 'Unit Relasi');

        $rencana = RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => 'Pelatihan Kepemimpinan',
            'target_kompetensi' => 'Kepemimpinan',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan jabatan',
            'catatan' => null,
            'status' => 'planned',
        ]);

        $diklat = Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Pelatihan Kepemimpinan',
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-REL-001',
            'tgl_sttpp' => '2026-05-09',
            'file_sertifikat_diklat' => null,
        ]);

        $pegawai = $pegawai->fresh(['rencanaDiklat', 'diklat']);
        $rencana = $rencana->fresh(['pegawai', 'diklat']);
        $diklat = $diklat->fresh(['pegawai', 'rencanaDiklat']);

        $this->assertCount(1, $pegawai->rencanaDiklat);
        $this->assertCount(1, $pegawai->diklat);
        $this->assertTrue($pegawai->rencanaDiklat->first()->is($rencana));
        $this->assertTrue($pegawai->diklat->first()->is($diklat));
        $this->assertTrue($rencana->pegawai->is($pegawai));
        $this->assertTrue($rencana->diklat->is($diklat));
        $this->assertTrue($diklat->pegawai->is($pegawai));
        $this->assertTrue($diklat->rencanaDiklat->is($rencana));
    }

    private function createPegawai(string $username, string $email, string $nama, string $unitName): Pegawai
    {
        $user = User::create([
            'username' => $username,
            'name' => $nama,
            'email' => $email,
            'role' => 'pegawai',
            'password' => bcrypt('password'),
        ]);

        $unitKerja = UnitKerja::create([
            'nama_unit' => $unitName,
            'alamat' => 'Jl. Relasi No. 1',
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
            'email_gov' => 'pegawai.relasi@gov.test',
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
}
