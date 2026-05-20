<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RencanaDiklatNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_sidebar_link_filter_form_and_visual_status_badges(): void
    {
        [$admin, $pegawaiA, $pegawaiB] = $this->seedAdminAndPegawai();

        $this->createRencana($pegawaiA, 'Rencana Planned', 'planned', '2026');
        $this->createRencana($pegawaiA, 'Rencana Draft', 'draft', '2026');
        $this->createRencana($pegawaiA, 'Rencana Realized', 'realized', '2027');
        $this->createRencana($pegawaiB, 'Rencana Cancelled', 'cancelled', '2028');

        $this->actingAs($admin)
            ->get(route('rencana_diklat.index'))
            ->assertOk()
            ->assertSeeHtml('data-testid="sidebar-rencana-diklat-link"')
            ->assertSeeHtml('data-testid="sidebar-report-diklat-gap-link"')
            ->assertSeeHtml('href="' . route('report.diklat_gap') . '"')
            ->assertSeeHtml('data-testid="sidebar-report-diklat-gap-unit-link"')
            ->assertSeeHtml('href="' . route('report.diklat_gap.unit') . '"')
            ->assertSee('Laporan')
            ->assertSee('Laporan Kesenjangan Diklat Pegawai')
            ->assertSee('Laporan Kesenjangan Diklat Unit')
            ->assertDontSeeHtml('>Report</span>')
            ->assertSeeHtml('data-testid="rencana-diklat-filter-form"')
            ->assertSeeHtml('data-testid="rencana-diklat-filter-search"')
            ->assertSeeHtml('data-testid="rencana-diklat-filter-pegawai"')
            ->assertSeeHtml('data-testid="rencana-diklat-filter-tahun"')
            ->assertSeeHtml('data-testid="rencana-diklat-filter-status"')
            ->assertSeeHtml('data-testid="rencana-diklat-status-badge"')
            ->assertSee('Buat dan pantau penugasan diklat dari admin/instansi')
            ->assertSee('Daftar ini adalah sumber penugasan awal')
            ->assertSeeHtml('data-status="draft"')
            ->assertSeeHtml('data-status="planned"')
            ->assertSeeHtml('data-status="realized"')
            ->assertSeeHtml('data-status="cancelled"')
            ->assertSee('Draf')
            ->assertSee('Direncanakan')
            ->assertSee('Terealisasi')
            ->assertSee('Dibatalkan');
    }

    public function test_rencana_filter_form_filters_results_with_query_parameters(): void
    {
        [$admin, $pegawaiA, $pegawaiB] = $this->seedAdminAndPegawai();

        $this->createRencana($pegawaiA, 'Rencana Cocok', 'planned', '2026');
        $this->createRencana($pegawaiA, 'Rencana Status Lain', 'draft', '2026');
        $this->createRencana($pegawaiB, 'Rencana Pegawai Lain', 'planned', '2027');

        $this->actingAs($admin)
            ->get(route('rencana_diklat.index', [
                'search' => 'Cocok',
                'pegawai_id' => $pegawaiA->id,
                'tahun_rencana' => '2026',
                'status' => 'planned',
            ]))
            ->assertOk()
            ->assertSee('Rencana Cocok')
            ->assertDontSee('Rencana Status Lain')
            ->assertDontSee('Rencana Pegawai Lain');
    }

    public function test_diklat_badges_and_pegawai_read_only_affordances_are_preserved(): void
    {
        [$admin, $pegawaiUser, $pegawai] = $this->seedSinglePegawaiFixture();

        $linkedPlan = $this->createRencana($pegawai, 'Rencana Linked', 'realized', '2026');
        $crossYearPlan = $this->createRencana($pegawai, 'Rencana Cross Year', 'realized', '2026');

        $linkedDiklat = $this->createDiklat($pegawai, 'Diklat Linked', '2026', $linkedPlan->id);
        $crossYearDiklat = $this->createDiklat($pegawai, 'Diklat Cross Year', '2027', $crossYearPlan->id);
        $outOfPlanDiklat = $this->createDiklat($pegawai, 'Diklat Out of Plan', '2026');

        $this->actingAs($admin)
            ->get('/kepegawaian/diklat')
            ->assertOk()
            ->assertSee('Data Diklat Resmi')
            ->assertSee('Menampilkan')
            ->assertSee('data diklat resmi')
            ->assertSee('Realisasi diklat yang sudah dilaksanakan dan tercatat sebagai riwayat resmi pegawai.')
            ->assertSee('Jumlah JP')
            ->assertSee('Status Rencana')
            ->assertSee('Unduh Sertifikat')
            ->assertSeeHtml('data-testid="diklat-link-status-badge"')
            ->assertSeeHtml('data-link-state="linked"')
            ->assertSeeHtml('data-link-state="out_of_plan"')
            ->assertSeeHtml('data-testid="diklat-link-status-cross_year"')
            ->assertSee($linkedDiklat->nama_diklat)
            ->assertSee($crossYearDiklat->nama_diklat)
            ->assertSee($outOfPlanDiklat->nama_diklat);

        $this->actingAs($pegawaiUser)
            ->get(route('rencana_diklat.index'))
            ->assertRedirect(route('diklat_saya.index'));

        $this->actingAs($pegawaiUser)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSee(route('diklat_saya.index'))
            ->assertSee('Diklat Saya')
            ->assertSeeHtml('data-testid="diklat-saya-rencana-table"')
            ->assertDontSee(route('rencana_diklat.edit', $linkedPlan))
            ->assertDontSee(route('rencana_diklat.destroy', $linkedPlan));

        $this->actingAs($pegawaiUser)
            ->get('/kepegawaian/diklat')
            ->assertRedirect(route('diklat_saya.index'));

        $this->actingAs($pegawaiUser)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSee('Diklat Saya')
            ->assertDontSee('/kepegawaian/diklat/view_form_tambah_diklat')
            ->assertDontSee('/kepegawaian/diklat/view_form_edit_diklat/' . $linkedDiklat->id)
            ->assertDontSee('/kepegawaian/diklat/delete_data_diklat/' . $linkedDiklat->id);
    }

    private function seedAdminAndPegawai(): array
    {
        $unit = UnitKerja::create([
            'nama_unit' => 'Unit Navigasi',
            'alamat' => 'Jl. Navigasi',
        ]);

        $admin = User::create([
            'username' => 'admin-navigation',
            'name' => 'Admin Navigation',
            'email' => 'admin.navigation@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unit->id,
            'password' => bcrypt('password'),
        ]);

        [, $pegawaiA] = $this->createPegawaiUser('pegawai-navigation-a', 'pegawai.navigation.a@example.test', 'Pegawai Navigasi A', $unit);
        [, $pegawaiB] = $this->createPegawaiUser('pegawai-navigation-b', 'pegawai.navigation.b@example.test', 'Pegawai Navigasi B', $unit);

        return [$admin, $pegawaiA, $pegawaiB];
    }

    private function seedSinglePegawaiFixture(): array
    {
        $unit = UnitKerja::create([
            'nama_unit' => 'Unit Read Only',
            'alamat' => 'Jl. Read Only',
        ]);

        $admin = User::create([
            'username' => 'admin-read-only',
            'name' => 'Admin Read Only',
            'email' => 'admin.readonly@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unit->id,
            'password' => bcrypt('password'),
        ]);

        [$pegawaiUser, $pegawai] = $this->createPegawaiUser('pegawai-read-only', 'pegawai.readonly@example.test', 'Pegawai Read Only', $unit);

        return [$admin, $pegawaiUser, $pegawai];
    }

    private function createPegawaiUser(string $username, string $email, string $nama, UnitKerja $unitKerja): array
    {
        $user = User::create([
            'username' => $username,
            'name' => $nama,
            'email' => $email,
            'role' => 'pegawai',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto.jpg',
            'nip' => (string) random_int(198801012020011001, 198801012020011999),
            'nik' => (string) random_int(3276010101880001, 3276010101889999),
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

        return [$user, $pegawai];
    }

    private function createRencana(Pegawai $pegawai, string $nama, string $status, string $tahun): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => $tahun,
            'nama_diklat_rencana' => $nama,
            'target_kompetensi' => 'Kepemimpinan',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan jabatan',
            'catatan' => null,
            'status' => $status,
        ]);
    }

    private function createDiklat(Pegawai $pegawai, string $nama, string $tahun, ?int $rencanaDiklatId = null): Diklat
    {
        return Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencanaDiklatId,
            'nama_diklat' => $nama,
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => $tahun,
            'no_sttpp' => 'STTPP-' . substr(md5($nama . $tahun), 0, 10),
            'tgl_sttpp' => $tahun . '-05-09',
            'file_sertifikat_diklat' => '/storage/document/' . substr(md5($nama . $tahun), 0, 10) . '.pdf',
        ]);
    }
}
