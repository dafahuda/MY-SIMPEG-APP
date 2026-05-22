<?php

namespace Tests\Feature\AdminSuperadminUiux;

use App\Models\MasterGolongan;
use App\Models\MasterPangkat;
use App\Models\Pangkat;
use App\Models\Pegawai;
use App\Models\RiwayatPendidikanLanjut;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Security\Support\RoleMatrixFixtures;
use Tests\TestCase;

class ReportCorrectnessPerformanceFixtureScaffoldTest extends TestCase
{
    use RefreshDatabase;
    use RoleMatrixFixtures;

    public function test_education_rank_index_zero_sd_fixture_contract_is_explicit(): void
    {
        $rankOrder = $this->educationRankOrderFixture();

        $this->assertSame('SD', $rankOrder[0], 'Fixture must pin SD at index 0 to guard against falsy index coercion.');
        $this->assertSame(0, array_search('SD', $rankOrder, true));
        $this->assertSame(0, $this->strictEducationRankIndex('sd', $rankOrder));
        $this->assertSame(9, $this->strictEducationRankIndex('S2', $rankOrder));
        $this->assertSame(-1, $this->strictEducationRankIndex('UNKNOWN', $rankOrder));
    }

    public function test_education_rank_index_zero_sd_is_valid_in_production_report_mapping(): void
    {
        [$admin, $unit] = $this->createPersistedAdminFixture();
        $pegawai = $this->createPersistedPegawai($unit, [
            'nama' => 'Pegawai Pendidikan SD',
            'jenis_kelamin' => 'laki-laki',
        ]);

        RiwayatPendidikanSekolah::create([
            'pegawai_id' => $pegawai->id,
            'jenjang_pendidikan' => 'SD',
            'nama_sekolah_universitas' => 'SD Negeri Fixture',
            'lokasi' => 'Fixture',
            'jurusan' => '-',
            'no_ijazah' => 'SD-001',
            'tgl_ijazah' => '2001-06-01',
            'nama_kepsek_rektor' => 'Kepala SD',
        ]);

        $response = $this->actingAs($admin)->get(route('report.nominatif', [
            'unit_kerja_id' => $unit->id,
        ]));

        $response->assertOk()
            ->assertSee('Pegawai Pendidikan SD')
            ->assertSee('SD Negeri Fixture')
            ->assertSee('SD');
    }

    public function test_unknown_null_gender_bucket_fixture_is_not_silently_counted_as_perempuan(): void
    {
        $bucket = $this->genderBucketFixture([
            'laki-laki',
            'perempuan',
            null,
            '',
            'unknown',
            '  Perempuan ',
            'Laki-Laki',
        ]);

        $this->assertSame(2, $bucket['laki']);
        $this->assertSame(2, $bucket['perempuan']);
        $this->assertSame(3, $bucket['unknown']);
    }

    public function test_unknown_null_gender_values_are_not_counted_as_perempuan_in_production_stats(): void
    {
        [$admin, $unit] = $this->createPersistedAdminFixture();

        $this->createPersistedPegawai($unit, [
            'nama' => 'Pegawai Laki',
            'jenis_kelamin' => 'laki-laki',
        ]);
        $this->createPersistedPegawai($unit, [
            'nama' => 'Pegawai Perempuan',
            'jenis_kelamin' => 'perempuan',
        ]);
        $emptyGender = $this->createPersistedPegawai($unit, [
            'nama' => 'Pegawai Empty Gender',
            'jenis_kelamin' => 'laki-laki',
        ]);
        $unknownGender = $this->createPersistedPegawai($unit, [
            'nama' => 'Pegawai Unknown Gender',
            'jenis_kelamin' => 'perempuan',
        ]);

        DB::statement("SET SESSION sql_mode = REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', '')");
        DB::table('tb_pegawai')->where('id', $emptyGender->id)->update(['jenis_kelamin' => '']);
        DB::table('tb_pegawai')->where('id', $unknownGender->id)->update(['jenis_kelamin' => 'unknown']);

        $response = $this->actingAs($admin)->get(route('report.keadaan_pegawai', [
            'unit_kerja_id' => $unit->id,
        ]));

        $stats = $response->viewData('stats');

        $response->assertOk();
        $this->assertSame(1, $stats['cnt']['laki']['staff']);
        $this->assertSame(1, $stats['cnt']['perempuan']['staff']);
        $this->assertSame(4, $stats['total']);
    }

    public function test_pegawai_report_direct_access_contract_is_forbidden(): void
    {
        [$pegawaiUser] = $this->createPegawaiUser(
            'fixture-pegawai-report',
            'fixture.pegawai.report@example.test',
            'Fixture Pegawai Report'
        );

        $this->actingAs($pegawaiUser)
            ->get('/report/nominatif?unit_kerja_id=1')
            ->assertForbidden();
    }

    public function test_report_query_guard_n_plus_one_scaffold_has_representative_budget_fixture(): void
    {
        $guard = $this->reportQueryGuardFixture();

        $this->assertSame(
            ['report.nominatif', 'report.duk', 'report.keadaan_pegawai', 'report.bezetting'],
            array_keys($guard)
        );

        foreach ($guard as $routeName => $meta) {
            $this->assertArrayHasKey('max_select_queries', $meta, "Missing query cap metadata for {$routeName}");
            $this->assertArrayHasKey('notes', $meta, "Missing notes metadata for {$routeName}");
            $this->assertGreaterThan(0, $meta['max_select_queries'], "Query cap must be positive for {$routeName}");
            $this->assertIsString($meta['notes']);
            $this->assertNotSame('', trim($meta['notes']));
        }
    }

    public function test_representative_report_queries_stay_within_budget_for_multiple_pegawai(): void
    {
        [$admin, $unit] = $this->createPersistedAdminFixture();
        [$masterPangkat, $masterGolongan] = $this->createPangkatMasters();

        foreach (range(1, 6) as $index) {
            $pegawai = $this->createPersistedPegawai($unit, [
                'nama' => "Pegawai Query {$index}",
                'jenis_kelamin' => $index % 2 === 0 ? 'perempuan' : 'laki-laki',
            ]);

            Pangkat::create([
                'pegawai_id' => $pegawai->id,
                'master_pangkat_id' => $masterPangkat->id,
                'master_golongan_id' => $masterGolongan->id,
                'jenis_pangkat' => 'Reguler',
                'tmt_pangkat_mulai' => '2020-01-01',
                'tmt_pangkat_selesai' => '2025-01-01',
                'no_sk' => "SK-{$index}",
                'tgl_sk' => '2020-01-01',
                'pejabat_pengesah_sk' => 'Pejabat',
            ]);

            RiwayatPendidikanSekolah::create([
                'pegawai_id' => $pegawai->id,
                'jenjang_pendidikan' => 'SD',
                'nama_sekolah_universitas' => "SD Query {$index}",
                'lokasi' => 'Fixture',
                'jurusan' => '-',
                'no_ijazah' => "SDQ-{$index}",
                'tgl_ijazah' => '2001-06-01',
                'nama_kepsek_rektor' => 'Kepala SD',
            ]);

            RiwayatPendidikanLanjut::create([
                'pegawai_id' => $pegawai->id,
                'jenjang_pendidikan' => 'S1',
                'nama_sekolah_universitas' => "Universitas Query {$index}",
                'jurusan' => 'Administrasi',
                'thn_mulai' => '2010',
                'thn_selesai' => '2014',
                'status' => 'Ijin Belajar',
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($admin)->get(route('report.duk', [
            'unit_kerja_id' => $unit->id,
        ]));

        $selectQueries = collect(DB::getQueryLog())
            ->filter(fn (array $query) => str_starts_with(strtolower(trim($query['query'])), 'select'))
            ->count();

        DB::disableQueryLog();

        $response->assertOk()->assertSee('Pegawai Query 1');
        $this->assertLessThanOrEqual(
            $this->reportQueryGuardFixture()['report.duk']['max_select_queries'],
            $selectQueries,
            "DUK report issued {$selectQueries} SELECT queries for six pegawai."
        );
    }

    private function educationRankOrderFixture(): array
    {
        return ['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
    }

    private function strictEducationRankIndex(?string $jenjang, array $rankOrder): int
    {
        if ($jenjang === null) {
            return -1;
        }

        $needle = strtoupper(trim($jenjang));
        $index = array_search($needle, $rankOrder, true);

        return $index === false ? -1 : $index;
    }

    private function genderBucketFixture(array $genders): array
    {
        $bucket = ['laki' => 0, 'perempuan' => 0, 'unknown' => 0];

        foreach ($genders as $gender) {
            $normalized = is_string($gender) ? strtolower(trim($gender)) : null;

            if ($normalized === 'laki-laki') {
                $bucket['laki']++;
                continue;
            }

            if ($normalized === 'perempuan') {
                $bucket['perempuan']++;
                continue;
            }

            $bucket['unknown']++;
        }

        return $bucket;
    }

    private function reportQueryGuardFixture(): array
    {
        return [
            'report.nominatif' => [
                'max_select_queries' => 8,
                'notes' => 'Representative guard for list + pangkat/pendidikan lookup collapse; flags N+1 growth.',
            ],
            'report.duk' => [
                'max_select_queries' => 10,
                'notes' => 'Representative guard for DUK route using same enrichment pattern as nominatif; fixed cap covers constant relation queries without per-pegawai growth.',
            ],
            'report.keadaan_pegawai' => [
                'max_select_queries' => 10,
                'notes' => 'Representative guard for grouped keadaan stats with preloaded maps.',
            ],
            'report.bezetting' => [
                'max_select_queries' => 8,
                'notes' => 'Representative guard for bezetting enrichment and rank projection.',
            ],
        ];
    }

    private function createPersistedAdminFixture(): array
    {
        $unit = UnitKerja::factory()->create([
            'nama_unit' => 'Unit Report Fixture',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'unit_kerja_id' => $unit->id,
            'email_verified_at' => now(),
        ]);

        return [$admin, $unit];
    }

    private function createPersistedPegawai(UnitKerja $unit, array $overrides = []): Pegawai
    {
        return Pegawai::factory()->create(array_merge([
            'unit_kerja_id' => $unit->id,
            'nama' => fake()->unique()->name(),
            'jenis_kelamin' => 'laki-laki',
            'status_kepegawaian' => 'PNS',
        ], $overrides));
    }

    private function createPangkatMasters(): array
    {
        $masterPangkat = MasterPangkat::create([
            'nama_pangkat' => 'Penata Muda',
        ]);

        $masterGolongan = MasterGolongan::create([
            'nama_golongan' => 'III/a',
        ]);

        return [$masterPangkat, $masterGolongan];
    }
}
