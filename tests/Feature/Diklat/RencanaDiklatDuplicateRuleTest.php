<?php

namespace Tests\Feature\Diklat;

use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RencanaDiklatDuplicateRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_planned_duplicate_is_blocked_for_same_employee_year_and_name(): void
    {
        $pegawai = $this->createPegawai();

        RencanaDiklat::create($this->payload($pegawai->id, 'planned'));

        $this->expectException(ValidationException::class);

        RencanaDiklat::create($this->payload($pegawai->id, 'planned'));
    }

    public function test_draft_duplicate_is_allowed_for_same_employee_year_and_name(): void
    {
        $pegawai = $this->createPegawai();

        RencanaDiklat::create($this->payload($pegawai->id, 'draft'));
        RencanaDiklat::create($this->payload($pegawai->id, 'draft'));

        $this->assertDatabaseCount('tb_rencana_diklat', 2);
    }

    public function test_db_constraint_blocks_duplicate_active_plan_when_bypassing_model_hooks(): void
    {
        $pegawai = $this->createPegawai();
        $payload = $this->payload($pegawai->id, 'planned');

        DB::table('tb_rencana_diklat')->insert($payload + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('tb_rencana_diklat')->insert($payload + [
            'status' => 'realized',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_db_constraint_blocks_second_realization_for_same_plan_when_bypassing_model_hooks(): void
    {
        $pegawai = $this->createPegawai();

        $rencana = RencanaDiklat::create($this->payload($pegawai->id, 'planned'));

        DB::table('tb_diklat')->insert([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Diklat Pertama',
            'jumlah_jam' => 24,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-DB-1',
            'tgl_sttpp' => '2026-05-09',
            'file_sertifikat_diklat' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('tb_diklat')->insert([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Diklat Kedua',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Jakarta',
            'angkatan' => '2',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-DB-2',
            'tgl_sttpp' => '2026-06-09',
            'file_sertifikat_diklat' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function payload(int $pegawaiId, string $status): array
    {
        return [
            'pegawai_id' => $pegawaiId,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => 'Pelatihan Kepemimpinan Dasar',
            'target_kompetensi' => 'Kepemimpinan',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan jabatan',
            'catatan' => null,
            'status' => $status,
        ];
    }

    private function createPegawai(): Pegawai
    {
        $user = User::create([
            'username' => 'pegawai-uji',
            'name' => 'Pegawai Uji',
            'email' => 'pegawai.uji@example.test',
            'role' => 'pegawai',
            'password' => bcrypt('password'),
        ]);
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Pengembangan SDM',
            'alamat' => 'Jl. Merdeka No. 1',
        ]);

        return Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto.jpg',
            'nip' => '198801012020011001',
            'nik' => '3276010101880001',
            'nama' => 'Pegawai Uji',
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
            'email' => 'pegawai.uji@example.test',
            'email_gov' => 'pegawai.uji@gov.test',
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
