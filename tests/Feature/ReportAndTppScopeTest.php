<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\PrestasiKerja;
use App\Models\Tpp;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAndTppScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_cannot_open_admin_report_pages(): void
    {
        $pegawai = $this->makePegawai('pegawai');

        $this->actingAs($pegawai->user)->get('/report/nominatif')->assertForbidden();
        $this->actingAs($pegawai->user)->get('/report/duk')->assertForbidden();
        $this->actingAs($pegawai->user)->get('/report/bezetting')->assertForbidden();
        $this->actingAs($pegawai->user)->get('/rekapitulasi/golongan')->assertForbidden();
    }

    public function test_admin_report_page_is_pinned_to_own_unit(): void
    {
        $unitA = UnitKerja::factory()->create(['nama_unit' => 'Unit A']);
        $unitB = UnitKerja::factory()->create(['nama_unit' => 'Unit B']);

        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitA->id]);

        Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitA->id])->id,
            'unit_kerja_id' => $unitA->id,
            'nama' => 'Pegawai Unit A',
        ]);
        Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitB->id])->id,
            'unit_kerja_id' => $unitB->id,
            'nama' => 'Pegawai Unit B',
        ]);

        // Admin requests unit B explicitly; scope must pin to own unit A.
        $response = $this->actingAs($admin)->get('/report/nominatif?unit_kerja_id=' . $unitB->id);

        $response->assertOk();
        $response->assertSee('Pegawai Unit A');
        $response->assertDontSee('Pegawai Unit B');
    }

    public function test_pegawai_cannot_update_other_employees_tpp(): void
    {
        [$pegawai, $other] = $this->seedPegawaiPair();

        $tpp = Tpp::create([
            'pegawai_id' => $other->id,
            'periode' => 'Januari',
            'tahun' => '2026',
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

        $response = $this->actingAs($pegawai->user)->put('/tpp/update_tpp/' . $tpp->id, [
            'pegawai_id' => $other->id,
            'periode' => 'Januari',
            'tahun' => '2026',
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
            'tidak_masuk_kerja' => 9,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('tb_tpp', ['id' => $tpp->id, 'tidak_masuk_kerja' => 0]);
    }

    public function test_pegawai_cannot_update_other_employees_prestasi_kerja(): void
    {
        [$pegawai, $other] = $this->seedPegawaiPair();

        $prestasi = PrestasiKerja::create([
            'pegawai_id' => $other->id,
            'periode_nilai_dari' => '2026-01-01',
            'periode_nilai_sampai' => '2026-12-31',
            'tahun_periode' => '2026',
            'nama_pejabat_nilai' => 'Pejabat',
            'nama_atasan_pejabat_penilai' => 'Atasan',
            'tgl_pejabat_penilai' => '2026-01-01',
            'isi_tanggapan' => 'Ditanggapi',
            'skp' => 90,
            'orientasi_pelayanan' => 90,
            'integritas' => 90,
            'komitmen' => 90,
            'disiplin' => 90,
            'kerjasama' => 90,
            'kepemimpinan' => 90,
            'tgl_keberatan_pegawai' => '2026-01-01',
            'isi_keberatan' => 'Tidak ada',
            'tgl_keputusan_atasan_pejabat_penilai' => '2026-01-02',
            'isi_keputusan' => 'Diterima',
            'rekomendasi' => 'Baik',
            'tgl_diterima_pegawai' => '2026-01-03',
            'tgl_diterima_atasan' => '2026-01-04',
        ]);

        $response = $this->actingAs($pegawai->user)->put('/skp_prestasi_kerja/edit_prestasi_kerja/' . $prestasi->id, [
            'pegawai_id' => $other->id,
            'periode_nilai_dari' => '2026-01-01',
            'periode_nilai_sampai' => '2026-12-31',
            'tahun_periode' => '2026',
            'nama_pejabat_nilai' => 'Pejabat',
            'nama_atasan_pejabat_penilai' => 'Atasan',
            'skp' => 50,
            'orientasi_pelayanan' => 50,
            'integritas' => 50,
            'komitmen' => 50,
            'disiplin' => 50,
            'kerjasama' => 50,
            'kepemimpinan' => 50,
            'tgl_keberatan_pegawai' => '2026-01-01',
            'isi_keberatan' => 'Tidak ada',
            'tgl_keputusan_atasan_pejabat_penilai' => '2026-01-02',
            'isi_keputusan' => 'Diterima',
            'rekomendasi' => 'Baik',
            'tgl_diterima_pegawai' => '2026-01-03',
        ]);

        $response->assertForbidden();
    }

    public function test_pegawai_laporan_bulanan_only_shows_own_rows(): void
    {
        [$pegawai, $other] = $this->seedPegawaiPair();

        foreach ([$pegawai, $other] as $p) {
            Tpp::create([
                'pegawai_id' => $p->id,
                'periode' => 'Januari',
                'tahun' => '2026',
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

        $response = $this->actingAs($pegawai->user)->get('/tpp/laporan_bulanan?periode=Januari&tahun=2026');

        $response->assertOk();
        $response->assertSee($pegawai->nama);
        $response->assertDontSee($other->nama);
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

    private function seedPegawaiPair(): array
    {
        $unit = UnitKerja::factory()->create();

        $pegawai = Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id])->id,
            'unit_kerja_id' => $unit->id,
            'nama' => 'Pegawai Sendiri',
        ]);

        $other = Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id])->id,
            'unit_kerja_id' => $unit->id,
            'nama' => 'Pegawai Orang Lain',
        ]);

        return [$pegawai, $other];
    }
}
