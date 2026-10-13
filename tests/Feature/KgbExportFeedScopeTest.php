<?php

namespace Tests\Feature;

use App\Exports\KGBExport;
use App\Exports\TppExport;
use App\Models\Pegawai;
use App\Models\Tpp;
use App\Models\UnitKerja;
use App\Models\User;
use App\Support\ExcelSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KgbExportFeedScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_cannot_open_kgb_create_or_export_routes(): void
    {
        $pegawai = $this->makePegawai('pegawai');

        $this->actingAs($pegawai->user)->get('/notifikasi_kgb/view_buat_kgb?pegawai_id=' . $pegawai->id)->assertForbidden();
        $this->actingAs($pegawai->user)->post('/notifikasi_kgb/buat_kgb', [
            'pegawai_id' => $pegawai->id,
            'periode' => '2026',
        ])->assertForbidden();
        $this->actingAs($pegawai->user)->get('/notifikasi_kgb/export_excel_kgb')->assertForbidden();
    }

    public function test_admin_cannot_create_kgb_for_other_unit(): void
    {
        [$unitA, $unitB] = $this->seedTwoUnits();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitA->id]);

        $otherUnitPegawai = Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitB->id])->id,
            'unit_kerja_id' => $unitB->id,
            'nama' => 'Pegawai Unit B',
        ]);

        $ownUnitPegawai = Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitA->id])->id,
            'unit_kerja_id' => $unitA->id,
            'nama' => 'Pegawai Unit A',
        ]);

        $this->actingAs($admin)
            ->get('/notifikasi_kgb/view_buat_kgb?pegawai_id=' . $otherUnitPegawai->id)
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/notifikasi_kgb/view_buat_kgb?pegawai_id=' . $ownUnitPegawai->id)
            ->assertOk();
    }

    public function test_kgb_export_respects_unit_scope(): void
    {
        [$unitA, $unitB] = $this->seedTwoUnits();
        $this->makePegawaiWithUnit('Pegawai Unit A', $unitA, tmtPns: '2022-01-01');
        $this->makePegawaiWithUnit('Pegawai Unit B', $unitB, tmtPns: '2022-01-01');

        $scoped = (new KGBExport('tahun_ini', 2026, $unitA->id))->collection();

        $this->assertCount(1, $scoped);
        $this->assertStringContainsString('Pegawai Unit A', $scoped->first()['Nama']);
    }

    public function test_tpp_export_respects_unit_and_pegawai_scope(): void
    {
        [$unitA, $unitB] = $this->seedTwoUnits();
        $pegA = $this->makePegawaiWithUnit('Pegawai Unit A', $unitA);
        $pegB = $this->makePegawaiWithUnit('Pegawai Unit B', $unitB);

        foreach ([$pegA, $pegB] as $p) {
            $this->makeTpp($p, 'Januari', '2026');
        }

        $unitScoped = (new TppExport('Januari', '2026', $unitA->id))->collection();
        $this->assertCount(1, $unitScoped);
        $this->assertSame('Pegawai Unit A', $unitScoped->first()['Nama Pegawai']);

        $ownScoped = (new TppExport('Januari', '2026', null, $pegB->id))->collection();
        $this->assertCount(1, $ownScoped);
        $this->assertSame('Pegawai Unit B', $ownScoped->first()['Nama Pegawai']);

        $unscoped = (new TppExport('Januari', '2026'))->collection();
        $this->assertCount(2, $unscoped);
    }

    public function test_excel_sanitizer_neutralizes_formula_injection(): void
    {
        $this->assertSame("'=cmd|'/c calc'!A1", ExcelSanitizer::cell("=cmd|'/c calc'!A1"));
        $this->assertSame("'+1+1", ExcelSanitizer::cell('+1+1'));
        $this->assertSame("'-2+2", ExcelSanitizer::cell('-2+2'));
        $this->assertSame("'@SUM(A1)", ExcelSanitizer::cell('@SUM(A1)'));
        $this->assertSame('Normal Name', ExcelSanitizer::cell('Normal Name'));
        $this->assertSame('', ExcelSanitizer::cell(null));
    }

    public function test_datafeed_rejects_non_integer_parameters(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($user)->getJson('/json-data-feed?datatype=abc')->assertStatus(422);
        $this->actingAs($user)->getJson('/json-data-feed?datatype=1&limit=999999')->assertStatus(422);
        $this->actingAs($user)->getJson('/json-data-feed?datatype=1')->assertOk();
    }

    public function test_profile_photo_upload_sanitizes_client_filename(): void
    {
        $unit = UnitKerja::factory()->create();
        $pegawai = Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id])->id,
            'unit_kerja_id' => $unit->id,
            'nama' => 'Pegawai Foto',
        ]);

        $file = UploadedFile::fake()->image('foto saya v2.png', 100, 100);

        $this->actingAs($pegawai->user)
            ->post('/profile_saya/upload_foto', ['foto' => $file])
            ->assertSessionHas('success');

        $stored = Pegawai::whereKey($pegawai->id)->value('foto');

        $this->assertStringStartsWith('/storage/images/', $stored);
        $this->assertStringContainsString('foto-saya-v2', $stored);
        $this->assertStringNotContainsString(' ', $stored);
        $this->assertMatchesRegularExpression('#^/storage/images/\d+_foto-saya-v2\.png$#', $stored);
    }

    private function seedTwoUnits(): array
    {
        return [
            UnitKerja::factory()->create(['nama_unit' => 'Unit Scope A']),
            UnitKerja::factory()->create(['nama_unit' => 'Unit Scope B']),
        ];
    }

    private function makePegawai(string $role): Pegawai
    {
        $unit = UnitKerja::factory()->create();

        return Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => $role, 'unit_kerja_id' => $unit->id])->id,
            'unit_kerja_id' => $unit->id,
            'nama' => 'Pegawai ' . $role,
        ]);
    }

    private function makePegawaiWithUnit(string $nama, UnitKerja $unit, ?string $tmtPns = null): Pegawai
    {
        return Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id])->id,
            'unit_kerja_id' => $unit->id,
            'nama' => $nama,
            'tmt_pns' => $tmtPns,
        ]);
    }

    private function makeTpp(Pegawai $pegawai, string $periode, string $tahun): Tpp
    {
        return Tpp::create([
            'pegawai_id' => $pegawai->id,
            'periode' => $periode,
            'tahun' => $tahun,
            'jml_hari_kerja' => 22,
            'tidak_ada_produktifitas' => 0,
            'terlambat_1_30' => 0,
            'terlambat_31_60' => 0,
            'terlambat_61_90' => 0,
            'terlambat_91_lebih' => 0,
            'pulang_awal_1_30' => 0,
            'pulang_awal_31_60' => 0,
            'pulang_awal_61_90' => 0,
            'pulang_awal_91_lebih' => 0,
            'tidak_masuk_kerja' => 0,
        ]);
    }
}
