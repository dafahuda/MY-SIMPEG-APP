<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RencanaDiklatMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rencana_diklat_migration_creates_nullable_foreign_key_and_index(): void
    {
        $this->assertTrue(Schema::hasTable('tb_rencana_diklat'));

        foreach ([
            'pegawai_id',
            'tahun_rencana',
            'nama_diklat_rencana',
            'target_kompetensi',
            'kategori_diklat',
            'prioritas',
            'target_jam',
            'target_penyelenggara',
            'alasan_kebutuhan',
            'catatan',
            'status',
            'active_duplicate_guard',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('tb_rencana_diklat', $column), "Missing column: {$column}");
        }

        $rencanaColumn = collect(Schema::getColumns('tb_diklat'))->firstWhere('name', 'rencana_diklat_id');

        $this->assertNotNull($rencanaColumn);
        $this->assertTrue((bool) ($rencanaColumn['nullable'] ?? false));

        $foreignKeys = collect(Schema::getForeignKeys('tb_diklat'));

        $this->assertTrue($foreignKeys->contains(function (array $foreignKey): bool {
            return $foreignKey['columns'] === ['rencana_diklat_id']
                && $foreignKey['foreign_table'] === 'tb_rencana_diklat'
                && $foreignKey['foreign_columns'] === ['id'];
        }));

        $indexes = collect(Schema::getIndexes('tb_rencana_diklat'));

        $this->assertTrue($indexes->contains(function (array $index): bool {
            return $index['name'] === 'tb_rencana_diklat_pegawai_tahun_status_index';
        }));

        $this->assertTrue($indexes->contains(function (array $index): bool {
            return $index['name'] === 'tb_rencana_diklat_active_guard_unique' && ($index['unique'] ?? false) === true;
        }));

        $diklatIndexes = collect(Schema::getIndexes('tb_diklat'));

        $this->assertTrue($diklatIndexes->contains(function (array $index): bool {
            return $index['name'] === 'tb_diklat_rencana_unique' && ($index['unique'] ?? false) === true;
        }));

        $pegawai = $this->createPegawai();
        $rencana = RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => 'Pelatihan Kepemimpinan Dasar',
            'target_kompetensi' => 'Kepemimpinan',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan jabatan',
            'catatan' => null,
            'status' => 'planned',
        ]);

        $linkedDiklat = Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Pelatihan Kepemimpinan Dasar',
            'jumlah_jam' => '40',
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-001',
            'tgl_sttpp' => '2026-05-09',
            'file_sertifikat_diklat' => null,
        ]);

        $legacyDiklat = Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => null,
            'nama_diklat' => 'Diklat Lama',
            'jumlah_jam' => '24',
            'penyelenggara' => 'BKD',
            'tempat' => 'Jakarta',
            'angkatan' => '2',
            'tahun' => '2025',
            'no_sttpp' => 'STTPP-LEG-001',
            'tgl_sttpp' => '2025-04-01',
            'file_sertifikat_diklat' => null,
        ]);

        $this->assertSame($rencana->id, $linkedDiklat->fresh()->rencana_diklat_id);
        $this->assertDatabaseHas('tb_diklat', [
            'id' => $legacyDiklat->id,
            'rencana_diklat_id' => null,
        ]);
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
