<?php

namespace Tests\Feature\Smoke;

use App\Models\Cuti;
use App\Models\Diklat;
use App\Models\Hukuman;
use App\Models\InstansiLembaga;
use App\Models\IzinKawin;
use App\Models\Jabatan;
use App\Models\LatihanJabatan;
use App\Models\MasterEselon;
use App\Models\MasterGolongan;
use App\Models\MasterJabatan;
use App\Models\MasterPangkat;
use App\Models\Mutasi;
use App\Models\Pangkat;
use App\Models\Pegawai;
use App\Models\Penghargaan;
use App\Models\PenugasanLuarNegeri;
use App\Models\PrestasiKerja;
use App\Models\RiwayatKeluargaAnak;
use App\Models\RiwayatKeluargaOrangtua;
use App\Models\RiwayatKeluargaSuamiIstri;
use App\Models\RiwayatPendidikanBahasa;
use App\Models\RiwayatPendidikanLanjut;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\Sekretariat;
use App\Models\Seminar;
use App\Models\Tpp;
use App\Models\Tunjangan;
use App\Models\UnitKerja;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Page-render smoke coverage for every GET page that no other feature test hits.
 *
 * Each case asserts HTTP 200 plus one stable marker string taken from the page's own
 * blade (heading or print-sheet title), so a broken partial or a renamed page fails
 * here instead of only in production.
 *
 * Record-bound routes get the same treatment one level deeper: the case builds a
 * factory-made row first, so model binding, the per-role unit scoping inside the
 * controller and the blade's relation reads are all exercised. Download endpoints
 * additionally get the physical file the controller looks for, so the assertion
 * covers a real stream instead of the "file missing" abort path.
 */
class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $superadmin;

    private User $pegawai;

    private UnitKerja $unitKerja;

    /** Pegawai record the bound-route cases hang their data off (admin's own unit). */
    private Pegawai $adminPegawai;

    protected function setUp(): void
    {
        parent::setUp();

        // Download controllers read Storage::disk('public'); keep their fixture
        // files out of the real storage/app/public tree.
        Storage::fake('public');

        $this->unitKerja = UnitKerja::factory()->create();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'unit_kerja_id' => $this->unitKerja->id,
        ]);
        $this->adminPegawai = Pegawai::factory()->create([
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

    protected function tearDown(): void
    {
        Storage::disk('public')->deleteDirectory('smoke');

        parent::tearDown();
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
     * Record-bound pages: each case builds its own factory row, so model
     * binding, controller unit scoping and relation reads are exercised.
     */
    #[DataProvider('boundPages')]
    public function test_record_bound_pages_render(Closure $fixture, string $marker, string $role): void
    {
        $user = $role === 'superadmin' ? $this->superadmin : $this->admin;
        $path = $fixture($this->adminPegawai);

        $response = $this->actingAs($user)->get($path);

        $this->assertSame(200, $response->getStatusCode(), "GET {$path}");
        $this->assertPageContains($response, $marker);
    }

    public static function boundPages(): array
    {
        return [
            'cuti_edit' => [static fn (Pegawai $p) => '/kepegawaian/cuti/view_form_edit_cuti/' . Cuti::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit riwayat cuti Pegawai Pilih', 'admin'],
            'diklat_edit' => [static fn (Pegawai $p) => '/kepegawaian/diklat/view_form_edit_diklat/' . Diklat::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit diklat Pegawai * Pilih', 'admin'],
            'hukuman_edit' => [static fn (Pegawai $p) => '/kepegawaian/hukuman/view_form_edit_hukuman/' . Hukuman::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit hukuman Pegawai * Pilih', 'admin'],
            'izinkawin_edit' => [static fn (Pegawai $p) => '/kepegawaian/izin_kawin/view_form_edit_izin_kawin/' . IzinKawin::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit surat izin kawin Pegawai', 'admin'],
            'jabatan_edit' => [static fn (Pegawai $p) => '/kepegawaian/jabatan/view_form_edit_jabatan/' . Jabatan::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit data jabatan Pegawai *', 'admin'],
            'latihanjabatan_edit' => [static fn (Pegawai $p) => '/kepegawaian/latihan_jabatan/view_form_edit_latihan_jabatan/' . LatihanJabatan::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit latihan jabatan Pegawai *', 'admin'],
            'mastereselon_edit' => [static fn (Pegawai $p) => '/kepegawaian/master_eselon/view_form_edit_master_eselon/' . MasterEselon::factory()->create()->id,
                'Form edit data master eselon Nama', 'admin'],
            'mastergolongan_edit' => [static fn (Pegawai $p) => '/kepegawaian/master_golongan/view_form_edit_master_golongan/' . MasterGolongan::factory()->create()->id,
                'Form edit data master golongan Nama', 'admin'],
            'masterjabatan_edit' => [static fn (Pegawai $p) => '/kepegawaian/master_jabatan/view_form_edit_master_jabatan/' . MasterJabatan::factory()->create()->id,
                'Form edit data master jabatan Nama', 'admin'],
            'masterpangkat_edit' => [static fn (Pegawai $p) => '/kepegawaian/master_pangkat/view_form_edit_master_pangkat/' . MasterPangkat::factory()->create()->id,
                'Form edit data master pangkat Nama', 'admin'],
            'mutasi_edit' => [static fn (Pegawai $p) => '/kepegawaian/mutasi/view_form_edit_mutasi/' . Mutasi::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit mutasi Pegawai * Pilih', 'admin'],
            'pangkat_edit' => [static fn (Pegawai $p) => '/kepegawaian/pangkat/view_form_edit_data_pangkat/' . Pangkat::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit data pangkat Pegawai Pilih', 'admin'],
            'penghargaan_edit' => [static fn (Pegawai $p) => '/kepegawaian/penghargaan/view_form_edit_penghargaan/' . Penghargaan::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit penghargaan pegawai Pegawai *', 'admin'],
            'penugasan_edit' => [static fn (Pegawai $p) => '/kepegawaian/penugasan_ln/view_form_edit_penugasan/' . PenugasanLuarNegeri::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit penugasan pegawai Pegawai *', 'admin'],
            'seminar_edit' => [static fn (Pegawai $p) => '/kepegawaian/seminar/view_form_edit_seminar/' . Seminar::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit seminar Pegawai * Pilih', 'admin'],
            'tunjangan_edit' => [static fn (Pegawai $p) => '/kepegawaian/tunjangan/view_form_edit_tunjangan/' . Tunjangan::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit tunjangan Pegawai * Pilih', 'admin'],
            'anak_edit' => [static fn (Pegawai $p) => '/riwayat_keluarga/anak/view_edit_data_anak/' . RiwayatKeluargaAnak::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit data riwayat anak Pegawai', 'admin'],
            'orangtua_edit' => [static fn (Pegawai $p) => '/riwayat_keluarga/orang_tua/view_form_edit_orang_tua/' . RiwayatKeluargaOrangtua::factory()->create(['pegawai_id' => $p->id])->id,
                'Form ubah data riwayat keluarga Orang', 'admin'],
            'suamistri_edit' => [static fn (Pegawai $p) => '/riwayat_keluarga/suami_istri/view_edit_data_suami_istri/' . RiwayatKeluargaSuamiIstri::factory()->create(['pegawai_id' => $p->id])->id,
                'Form ubah data riwayat keluarga suami', 'admin'],
            'bahasa_edit' => [static fn (Pegawai $p) => '/riwayat_pendidikan/pendidikan_bahasa/view_form_edit_pendidikan_bahasa/' . RiwayatPendidikanBahasa::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit data riwayat pendidikan bahasa', 'admin'],
            'sekolah_edit' => [static fn (Pegawai $p) => '/riwayat_pendidikan/sekolah/view_form_edit_pendidikan_sekolah/' . RiwayatPendidikanSekolah::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit data riwayat pendidikan sekolah', 'admin'],
            'lanjut_edit' => [static fn (Pegawai $p) => '/riwayat_pendidikan/sekolah_lanjut/view_form_edit_pendidikan_lanjut/' . RiwayatPendidikanLanjut::factory()->create(['pegawai_id' => $p->id])->id,
                'Form ubah data riwayat pendidikan lanjut', 'admin'],
            'prestasi_edit' => [static fn (Pegawai $p) => '/skp_prestasi_kerja/view_form_edit_prestasi_kerja/' . PrestasiKerja::factory()->create(['pegawai_id' => $p->id])->id,
                'Form edit prestasi kerja Pegawai Pilih', 'admin'],
            'tpp_edit' => [static fn (Pegawai $p) => '/tpp/edit_tpp/' . Tpp::factory()->create(['pegawai_id' => $p->id])->id,
                'Riwayat / Tambahan Penghasilan Pegawai /', 'admin'],
            'instansi_setup_edit' => [static fn (Pegawai $p) => '/manajemen_setup/setup_instansi_lembaga/' . \App\Models\InstansiLembaga::factory()->create()->id,
                'Setup Instansi Lembaga', 'superadmin'],
            'sekretariat_setup' => [static fn (Pegawai $p) => '/manajemen_setup/sekretariat/setup_sekretariat/' . Sekretariat::factory()->create()->id,
                'Setup Instansi Lembaga Nama sekretariat Kabupaten', 'superadmin'],
            'unitkerja_edit' => [static fn (Pegawai $p) => '/manajemen_setup/view_form_edit_unitkerja/' . UnitKerja::factory()->create()->id,
                'Form edit unit kerja Nama Unit', 'superadmin'],
            'useradmin_edit' => [static fn (Pegawai $p) => '/manajemen_setup/view_form_edit_user_admin/' . User::factory()->create(['role' => 'admin'])->id,
                'Form edit data user admin Username', 'superadmin'],
            'userpegawai_edit' => [static fn (Pegawai $p) => '/manajemen_setup/view_form_edit_user_pegawai/' . User::factory()->create(['role' => 'pegawai'])->id,
                'Form edit data user pegawai Username', 'admin'],
            'pegawai_detail' => [static fn (Pegawai $p) => '/data_pegawai/detail_data_pegawai/' . $p->id,
                'Detail Pegawai Data Pegawai', 'admin'],
            'pegawai_edit' => [static fn (Pegawai $p) => '/data_pegawai/view_form_edit_data_pegawai/' . $p->id,
                'Form edit pegawai', 'admin'],
        ];
    }

    /**
     * Download endpoints stream the physical file through the public disk;
     * each case seeds the file so the assertion covers a real attachment.
     */
    #[DataProvider('downloadPages')]
    public function test_download_endpoints_stream_attachment(Closure $fixture): void
    {
        foreach (['smoke/cuti.pdf', 'smoke/hukuman.pdf', 'smoke/diklat.pdf', 'smoke/latihan.pdf',
                  'smoke/pg.pdf', 'smoke/pt.pdf', 'smoke/seminar.pdf', 'smoke/mutasi.pdf'] as $file) {
            Storage::disk('public')->put($file, '%PDF-1.4 smoke fixture');
        }

        $path = $fixture($this->adminPegawai);
        $response = $this->actingAs($this->admin)->get($path);

        $response->assertOk();
        $this->assertStringStartsWith('attachment;', (string) $response->headers->get('Content-Disposition'), "GET {$path}");
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
    }

    public static function downloadPages(): array
    {
        return [
            'cuti' => [static fn (Pegawai $p) => '/kepegawaian/cuti/download_surat_cuti/' . Cuti::factory()->create(['pegawai_id' => $p->id, 'file_surat_cuti' => 'smoke/cuti.pdf'])->id],
            'hukuman' => [static fn (Pegawai $p) => '/kepegawaian/hukuman/download_sk/' . Hukuman::factory()->create(['pegawai_id' => $p->id, 'file_sk_hukuman' => 'smoke/hukuman.pdf'])->id],
            'diklat' => [static fn (Pegawai $p) => '/kepegawaian/diklat/download_sertifikat_diklat/' . Diklat::factory()->create(['pegawai_id' => $p->id, 'file_sertifikat_diklat' => 'smoke/diklat.pdf'])->id],
            'latihan_jabatan' => [static fn (Pegawai $p) => '/kepegawaian/latihan_jabatan/download_sertifikat/' . LatihanJabatan::factory()->create(['pegawai_id' => $p->id, 'file_sertifikat' => 'smoke/latihan.pdf'])->id],
            'penghargaan' => [static fn (Pegawai $p) => '/kepegawaian/penghargaan/download_sertifikat/' . Penghargaan::factory()->create(['pegawai_id' => $p->id, 'file_sertifikat_penghargaan' => 'smoke/pg.pdf'])->id],
            'penugasan_ln' => [static fn (Pegawai $p) => '/kepegawaian/penugasan_ln/download_surat_tugas/' . PenugasanLuarNegeri::factory()->create(['pegawai_id' => $p->id, 'st' => '/storage/smoke/pt.pdf'])->id],
            'seminar' => [static fn (Pegawai $p) => '/kepegawaian/seminar/download_piagam/' . Seminar::factory()->create(['pegawai_id' => $p->id, 'file_piagam' => 'smoke/seminar.pdf'])->id],
            'mutasi' => [static fn (Pegawai $p) => '/kepegawaian/mutasi/download_sk/' . Mutasi::factory()->create(['pegawai_id' => $p->id, 'file_sk_mutasi' => 'smoke/mutasi.pdf'])->id],
        ];
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
