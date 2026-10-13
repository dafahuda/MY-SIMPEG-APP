<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Page-render smoke coverage for every GET page that no other feature test hits.
 *
 * Each case asserts HTTP 200 plus one stable marker string taken from the page's own
 * blade (heading or print-sheet title), so a broken partial or a renamed page fails
 * here instead of only in production.
 */
class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superadmin;

    private User $pegawai;

    private UnitKerja $unitKerja;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitKerja = UnitKerja::factory()->create();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'unit_kerja_id' => $this->unitKerja->id,
        ]);
        Pegawai::factory()->create([
            'user_id' => $this->admin->id,
            'unit_kerja_id' => $this->unitKerja->id,
            'nama' => 'Pegawai Admin',
        ]);

        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'unit_kerja_id' => $this->unitKerja->id,
        ]);

        $this->pegawai = User::factory()->create([
            'role' => 'pegawai',
            'unit_kerja_id' => $this->unitKerja->id,
        ]);
        Pegawai::factory()->create([
            'user_id' => $this->pegawai->id,
            'unit_kerja_id' => $this->unitKerja->id,
            'nama' => 'Pegawai Biasa',
        ]);
    }

    #[DataProvider('superadminPages')]
    public function test_superadmin_pages_render(string $path, string $marker): void
    {
        $response = $this->actingAs($this->superadmin)->get($path);

        $response->assertOk();
        $this->assertPageContains($response, $marker);
    }

    public static function superadminPages(): array
    {
        return [
            'backup_data' => ['/backup_data', 'Backup Database'],
            'data_user_admin' => ['/manajemen_setup/data_user_admin', 'Data User Admin'],
            'instansi_lembaga' => ['/manajemen_setup/instansi_lembaga', 'Instansi Lembaga'],
            'instansi_lembaga_setup' => ['/manajemen_setup/instansi_lembaga/setup', 'Buat data instansi'],
            'opd_skpd_unitkerja' => ['/manajemen_setup/opd_skpd_unitkerja', 'Data OPD / SKPD / Unit Kerja'],
            'sekretariat' => ['/manajemen_setup/sekretariat', 'Sekretariat'],
            'sekretariat_create' => ['/manajemen_setup/sekretariat/create', 'Buat data sekretariat'],
            'view_form_tambah_unitkerja' => ['/manajemen_setup/view_form_tambah_unitkerja', 'Form tambah unit kerja'],
            'view_form_tambah_user_admin' => ['/manajemen_setup/view_form_tambah_user_admin', 'Form tambah data user admin'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admin_pages_render(string $path, string $marker): void
    {
        $response = $this->actingAs($this->admin)->get($path);

        $response->assertOk();
        $this->assertPageContains($response, $marker);
    }

    public static function adminPages(): array
    {
        return [
            'rekapitulasi_agama' => ['/rekapitulasi/agama', 'Rekapitulasi Data Pegawai Berdasarkan Agama'],
            'rekapitulasi_eselon' => ['/rekapitulasi/eselon', 'Rekapitulasi Data Eselon'],
            'rekapitulasi_jabatan' => ['/rekapitulasi/jabatan', 'Rekapitulasi Data Jabatan'],
            'rekapitulasi_jenis_kelamin' => ['/rekapitulasi/jenis_kelamin', 'Rekapitulasi Data Pegawai Berdasarkan Jenis Kelamin'],
            'rekapitulasi_opd_skpd_unit_kerja' => ['/rekapitulasi/opd_skpd_unit_kerja', 'Rekapitulasi Pegawai Berdasarkan OPD / SKPD / Unit Kerja'],
            'rekapitulasi_pangkat' => ['/rekapitulasi/pangkat', 'Rekapitulasi Data Pangkat'],
            'rekapitulasi_pendidikan_terakhir' => ['/rekapitulasi/pendidikan_terakhir', 'Rekapitulasi Data Pegawai Berdasarkan Pendidikan Akhir'],
            'rekapitulasi_status_kepegawaian' => ['/rekapitulasi/status_kepegawaian', 'Rekapitulasi Data Status Kepegawaian'],
            'rekapitulasi_status_pernikahan' => ['/rekapitulasi/status_pernikahan', 'Rekapitulasi Data Pegawai Berdasarkan Status Pernikahan'],
            'report_keadaan_pegawai' => ['/report/keadaan_pegawai', 'Report Keadaan Pegawai'],
            'report_pensiun' => ['/report/pensiun', 'Report Pensiun'],
        ];
    }

    /**
     * Pages behind no role middleware: reachable by any authenticated user.
     *
     * @return void
     */
    #[DataProvider('anyRolePages')]
    public function test_any_authenticated_role_pages_render(string $path, string $marker): void
    {
        foreach (['admin' => $this->admin, 'pegawai' => $this->pegawai] as $role => $user) {
            $response = $this->actingAs($user)->get($path);

            $this->assertSame(200, $response->getStatusCode(), "[{$role}] GET {$path}");
            $this->assertPageContains($response, $marker);
        }
    }

    public static function anyRolePages(): array
    {
        return [
            'cuti_view_form_tambah' => ['/kepegawaian/cuti/view_form_tambah_cuti', 'Form tambah riwayat cuti'],
            'hukuman' => ['/kepegawaian/hukuman', 'Data Hukuman'],
            'hukuman_view_form_tambah' => ['/kepegawaian/hukuman/view_form_tambah_hukuman', 'Form tambah hukuman'],
            'izin_kawin' => ['/kepegawaian/izin_kawin', 'Data Izin kawin'],
            'izin_kawin_view_form_tambah' => ['/kepegawaian/izin_kawin/view_form_tambah_izin_kawin', 'Form tambah surat izin kawin'],
            'jabatan' => ['/kepegawaian/jabatan', 'Data Jabatan'],
            'jabatan_view_tambah' => ['/kepegawaian/jabatan/view_tambah_jabatan', 'Form tambah data jabatan'],
            'latihan_jabatan' => ['/kepegawaian/latihan_jabatan', 'Data Riwayat Latihan Jabatan'],
            'latihan_jabatan_view_form_tambah' => ['/kepegawaian/latihan_jabatan/view_form_tambah_latihan_jabatan', 'Form tambah latihan jabatan'],
            'mutasi' => ['/kepegawaian/mutasi', 'Data Mutasi'],
            'mutasi_view_form_tambah' => ['/kepegawaian/mutasi/view_form_tambah_mutasi', 'Form tambah mutasi'],
            'pangkat' => ['/kepegawaian/pangkat', 'Data Pangkat'],
            'pangkat_view_form_tambah' => ['/kepegawaian/pangkat/view_form_tambah_pangkat', 'Form tambah data pangkat'],
            'penghargaan' => ['/kepegawaian/penghargaan', 'Data Penghargaan'],
            'penghargaan_view_form_tambah' => ['/kepegawaian/penghargaan/view_form_tambah_penghargaan', 'Form tambah penghargaan pegawai'],
            'penugasan_ln' => ['/kepegawaian/penugasan_ln', 'Data Penugasan'],
            'penugasan_ln_view_form_tambah' => ['/kepegawaian/penugasan_ln/view_form_tambah_penugasan', 'Form tambah penugasan pegawai'],
            'seminar' => ['/kepegawaian/seminar', 'Data Seminar'],
            'seminar_view_form_tambah' => ['/kepegawaian/seminar/view_form_tambah_seminar', 'Form tambah data seminar pegawai'],
            'tunjangan' => ['/kepegawaian/tunjangan', 'Data Tunjangan'],
            'tunjangan_view_form_tambah' => ['/kepegawaian/tunjangan/view_form_tambah_tunjangan', 'Form tambah tunjangan'],
            'data_notifikasi_kgb' => ['/notifikasi_kgb/data_notifikasi_kgb', 'Notifikasi'],
            'profile_saya_edit' => ['/profile_saya/edit', 'Edit Profile Pegawai'],
            'riwayat_keluarga_anak' => ['/riwayat_keluarga/anak', 'Data Riwayat Anak'],
            'riwayat_keluarga_anak_view_tambah' => ['/riwayat_keluarga/anak/view_tambah_data_anak', 'Form tambah data riwayat anak'],
            'riwayat_keluarga_orang_tua' => ['/riwayat_keluarga/orang_tua', 'Data Riwayat Keluarga Orang Tua Pegawai'],
            'riwayat_keluarga_orang_tua_view_form_tambah' => ['/riwayat_keluarga/orang_tua/view_form_tambah_orang_tua', 'Form tambah data riwayat keluarga Orang Tua Pegawai'],
            'riwayat_keluarga_suami_istri' => ['/riwayat_keluarga/suami_istri', 'Data Riwayat Keluarga Suami / Istri'],
            'riwayat_keluarga_suami_istri_view_form_tambah' => ['/riwayat_keluarga/suami_istri/view_form_tambah_suami_istri', 'Form tambah data riwayat keluarga suami / istri'],
            'riwayat_pendidikan_bahasa' => ['/riwayat_pendidikan/pendidikan_bahasa', 'Data Riwayat Pendidikan bahasa'],
            'riwayat_pendidikan_bahasa_view_form_tambah' => ['/riwayat_pendidikan/pendidikan_bahasa/view_form_tambah_pendidikan_bahasa', 'Form tambah data riwayat pendidikan bahasa'],
            'riwayat_pendidikan_sekolah' => ['/riwayat_pendidikan/sekolah', 'Data Riwayat Pendidikan Sekolah'],
            'riwayat_pendidikan_sekolah_view_form_tambah' => ['/riwayat_pendidikan/sekolah/view_form_tambah_pendidikan_sekolah', 'Form tambah data riwayat pendidikan sekolah'],
            'riwayat_pendidikan_sekolah_lanjut' => ['/riwayat_pendidikan/sekolah_lanjut', 'Data Riwayat Pendidikan Lanjut'],
            'riwayat_pendidikan_sekolah_lanjut_view_form_tambah' => ['/riwayat_pendidikan/sekolah_lanjut/view_form_tambah_pendidikan_lanjut', 'Form tambah data riwayat pendidikan lanjut'],
            'skp_prestasi_kerja_data' => ['/skp_prestasi_kerja/data_prestasi_kerja', 'Data Prestasi Kerja'],
            'skp_prestasi_kerja_view_form_tambah' => ['/skp_prestasi_kerja/view_form_tambah_prestasi_kerja', 'Form tambah prestasi kerja'],
            'tpp_input' => ['/tpp/input_tpp', 'Data TPP'],
            'tpp_tambah' => ['/tpp/tambah_tpp', 'Tambah Data TPP'],
        ];
    }

    /**
     * Admin print sheets render in full (the unit scope pins the request to the
     * admin's own unit kerja, so the sheet never needs an explicit filter).
     */
    #[DataProvider('printPages')]
    public function test_report_print_pages_render_html_for_admin(string $path, string $marker): void
    {
        $response = $this->actingAs($this->admin)->get($path);

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/html; charset=utf-8');
        $this->assertPageContains($response, $marker);
    }

    /**
     * Without a unit kerja the print controllers have nothing to render and send
     * the user back to the report filter page.
     */
    #[DataProvider('printPages')]
    public function test_report_print_pages_redirect_without_unit_for_superadmin(string $path, string $marker): void
    {
        $reportPage = str_replace('/print', '', $path);

        $this->actingAs($this->superadmin)
            ->get($path)
            ->assertRedirect(route('report.' . basename($reportPage)));
    }

    public static function printPages(): array
    {
        return [
            'nominatif_print' => ['/report/nominatif/print', 'DAFTAR NOMINATIF PEGAWAI NEGERI SIPIL'],
            'duk_print' => ['/report/duk/print', 'DAFTAR URUT KEPANGKATAN'],
            'bezetting_print' => ['/report/bezetting/print', 'DAFTAR BEZETTING PNS'],
            'keadaan_pegawai_print' => ['/report/keadaan_pegawai/print', 'JENIS LAPORAN'],
        ];
    }

    /**
     * The JSON feed is the only unauthenticated-shape endpoint in scope: it must
     * answer with application/json and the label/data payload the front end reads.
     */
    public function test_json_data_feed_returns_json(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/json-data-feed?datatype=1&limit=10');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure(['labels', 'data']);
    }

    /**
     * Blade headings wrap across lines, so raw substring matching on the
     * rendered HTML fails on text that is visually identical. Parse with
     * DOMDocument (tolerant of broken markup) and compare against the
     * <main> content only, so sidebar menu labels cannot satisfy a
     * page-heading marker by accident.
     */
    private function assertPageContains(TestResponse $response, string $marker): void
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $main = $doc->getElementsByTagName('main')->item(0);
        $text = preg_replace('/\s+/u', ' ', $main ? $main->textContent : $doc->textContent);
        $needle = preg_replace('/\s+/u', ' ', $marker);

        $this->assertStringContainsString($needle, trim($text), "Marker [{$marker}] missing in page main content");
    }
}
