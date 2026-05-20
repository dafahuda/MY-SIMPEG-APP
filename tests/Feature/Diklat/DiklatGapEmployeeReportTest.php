<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatGapEmployeeReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_defaults_tahun_realisasi_to_tahun_rencana(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawaiPair();

        $activePlan = $this->createRencana($pegawai, '2026', 'Rencana Aktif', 'planned', 40);
        $realizedPlan = $this->createRencana($pegawai, '2026', 'Rencana Realized', 'realized', 24);
        $this->createRencana($pegawai, '2026', 'Rencana Draft', 'draft', 12);
        $this->createRencana($pegawai, '2026', 'Rencana Cancelled', 'cancelled', 16);
        $this->createDiklat($pegawai, $realizedPlan, '2026', 'Diklat Realized');
        $this->createDiklat($pegawai, null, '2026', 'Diklat Lepas');

        $response = $this->actingAs($admin)->get(route('report.diklat_gap', [
            'tahun_rencana' => '2026',
            'pegawai_id' => $pegawai->id,
            'unit_kerja_id' => $pegawai->unit_kerja_id,
        ]));

        $response->assertOk()
            ->assertSee('Laporan')
            ->assertSee('Kesenjangan Diklat Pegawai')
            ->assertSee('pengajuan yang masih menunggu verifikasi atau revisi tidak dihitung sebagai riwayat resmi')
            ->assertSee('Gunakan Tahun Rencana')
            ->assertSee('Selisih Jam')
            ->assertSeeHtml('data-testid="diklat-gap-filter-form"')
            ->assertSeeHtml('data-testid="diklat-gap-tahun-realisasi-select" data-selected="2026"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-planned" data-value="2"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-not_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-out_of_plan" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-cross_year_realized" data-value="0"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-planned_hours" data-value="64"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-realized_linked_hours" data-value="24"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-hour_gap" data-value="40"')
            ->assertSee('Rencana Aktif')
            ->assertDontSee('Rencana Draft')
            ->assertDontSee('Rencana Cancelled')
            ->assertSee('Diklat Lepas')
            ->assertSeeHtml('data-testid="diklat-gap-section-out-of-plan"');

        $this->assertSame('Rencana Aktif', $activePlan->fresh()->nama_diklat_rencana);
    }

    public function test_report_renders_cross_year_and_out_of_plan_rows(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawaiPair();

        $crossYearPlan = $this->createRencana($pegawai, '2026', 'Rencana Lintas Tahun', 'realized', 30);
        $this->createDiklat($pegawai, $crossYearPlan, '2027', 'Diklat Lintas Tahun');
        $this->createDiklat($pegawai, null, '2027', 'Diklat Lepas 2027');

        $response = $this->actingAs($admin)->get(route('report.diklat_gap', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'pegawai_id' => $pegawai->id,
            'unit_kerja_id' => $pegawai->unit_kerja_id,
        ]));

        $response->assertOk()
            ->assertSee('Kesenjangan Diklat Pegawai')
            ->assertSee('Selisih Jam')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-cross_year_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-bucket-count-out-of-plan" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-row-cross-year-realized-0"')
            ->assertSeeHtml('data-testid="diklat-gap-row-out-of-plan-0"')
            ->assertSee('Diklat Lintas Tahun')
            ->assertSee('Diklat Lepas 2027');

        $this->assertSame('realized', $crossYearPlan->fresh()->status);
    }

    public function test_report_excludes_linked_realization_outside_selected_tahun_realisasi(): void
    {
        [$admin, $pegawai] = $this->seedAdminAndPegawaiPair();

        $crossYearPlan = $this->createRencana($pegawai, '2026', 'Rencana Tahun Lain', 'realized', 30);
        $this->createDiklat($pegawai, $crossYearPlan, '2027', 'Diklat Tahun Lain');

        $response = $this->actingAs($admin)->get(route('report.diklat_gap', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2026',
            'pegawai_id' => $pegawai->id,
            'unit_kerja_id' => $pegawai->unit_kerja_id,
        ]));

        $response->assertOk()
            ->assertSee('Kesenjangan Diklat Pegawai')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-planned" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-realized" data-value="0"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-not_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-summary-value-cross_year_realized" data-value="0"')
            ->assertDontSee('Diklat Tahun Lain');
    }

    private function seedAdminAndPegawaiPair(): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Employee Report',
            'alamat' => 'Jl. Employee Report No. 1',
        ]);

        $admin = User::create([
            'username' => 'admin-gap-report',
            'name' => 'Admin Gap Report',
            'email' => 'admin.gap.report@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawaiUser = User::create([
            'username' => 'pegawai-gap-report',
            'name' => 'Pegawai Gap Report',
            'email' => 'pegawai.gap.report@example.test',
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
            'nama' => 'Pegawai Gap Report',
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
            'email' => 'pegawai.gap.report@example.test',
            'email_gov' => 'pegawai.gap.report@gov.test',
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

    private function createRencana(Pegawai $pegawai, string $tahun, string $nama, string $status, int $targetJam): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => $tahun,
            'nama_diklat_rencana' => $nama,
            'target_kompetensi' => 'Kompetensi',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => $targetJam,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan',
            'catatan' => null,
            'status' => $status,
        ]);
    }

    private function createDiklat(Pegawai $pegawai, ?RencanaDiklat $rencana, string $tahun, string $nama): Diklat
    {
        return Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana?->id,
            'nama_diklat' => $nama,
            'jumlah_jam' => 24,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => $tahun,
            'no_sttpp' => 'STTPP-' . $nama,
            'tgl_sttpp' => $tahun . '-05-09',
            'file_sertifikat_diklat' => null,
        ]);
    }
}
