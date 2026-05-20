<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DiklatCrossYearRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_year_realization_is_allowed_for_the_same_employee(): void
    {
        $pegawai = $this->createPegawai('pegawai-same', 'pegawai.same@example.test');
        $rencana = $this->createRencana($pegawai, '2026');

        $diklat = $this->createDiklat($pegawai, $rencana, '2026');

        $this->assertDatabaseHas('tb_diklat', [
            'id' => $diklat->id,
            'rencana_diklat_id' => $rencana->id,
        ]);
    }

    public function test_next_year_realization_is_allowed_for_the_same_employee(): void
    {
        $pegawai = $this->createPegawai('pegawai-next', 'pegawai.next@example.test');
        $rencana = $this->createRencana($pegawai, '2026');

        $diklat = $this->createDiklat($pegawai, $rencana, '2027');

        $this->assertDatabaseHas('tb_diklat', [
            'id' => $diklat->id,
            'tahun' => '2027',
        ]);
    }

    public function test_realization_year_more_than_one_year_ahead_is_blocked(): void
    {
        $pegawai = $this->createPegawai('pegawai-far', 'pegawai.far@example.test');
        $rencana = $this->createRencana($pegawai, '2026');

        $this->expectException(ValidationException::class);

        $this->createDiklat($pegawai, $rencana, '2028');
    }

    public function test_realization_for_another_employee_is_blocked(): void
    {
        $pegawaiA = $this->createPegawai('pegawai-a', 'pegawai.a@example.test');
        $pegawaiB = $this->createPegawai('pegawai-b', 'pegawai.b@example.test');
        $rencana = $this->createRencana($pegawaiA, '2026');

        $this->expectException(ValidationException::class);

        $this->createDiklat($pegawaiB, $rencana, '2026');
    }

    public function test_one_plan_can_only_have_one_realization(): void
    {
        $pegawai = $this->createPegawai('pegawai-single', 'pegawai.single@example.test');
        $rencana = $this->createRencana($pegawai, '2026');

        $this->createDiklat($pegawai, $rencana, '2026');

        $this->expectException(ValidationException::class);

        $this->createDiklat($pegawai, $rencana, '2026');
    }

    private function createPegawai(string $username, string $email): Pegawai
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Cross Year',
            'alamat' => 'Jl. Cross No. 1',
        ]);

        $user = User::create([
            'username' => $username,
            'name' => strtoupper($username),
            'email' => $email,
            'role' => 'pegawai',
            'password' => bcrypt('password'),
        ]);

        return Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto.jpg',
            'nip' => '198801012020011001',
            'nik' => '3276010101880001',
            'nama' => strtoupper($username),
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

    private function createRencana(Pegawai $pegawai, string $tahun): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => $tahun,
            'nama_diklat_rencana' => 'Rencana ' . $pegawai->id,
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

    private function createDiklat(Pegawai $pegawai, RencanaDiklat $rencana, string $tahun): Diklat
    {
        return Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Diklat ' . $pegawai->id,
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => $tahun,
            'no_sttpp' => 'STTPP-' . $pegawai->id . '-' . $tahun,
            'tgl_sttpp' => $tahun . '-05-09',
            'file_sertifikat_diklat' => null,
        ]);
    }
}
