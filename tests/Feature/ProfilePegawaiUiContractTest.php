<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\User;
use App\Support\ProfilePegawaiUi;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProfilePegawaiUiContractTest extends TestCase
{

    public function test_profile_ui_helper_resolves_canonical_and_legacy_tabs(): void
    {
        $this->assertSame('profil', ProfilePegawaiUi::resolveTab(null));
        $this->assertSame('profil', ProfilePegawaiUi::resolveTab('unknown'));
        $this->assertSame('diklat', ProfilePegawaiUi::resolveTab('diklat'));
        $this->assertSame('diklat', ProfilePegawaiUi::resolveTab('artis'));
    }

    public function test_profile_page_has_canonical_diklat_tab_and_kepegawaian_controls(): void
    {
        $view = file_get_contents(resource_path('views/pages/dashboard/profile_pegawai/indexProfilePegawai.blade.php'));
        $helper = file_get_contents(app_path('Support/ProfilePegawaiUi.php'));

        $this->assertStringContainsString("'artis' => 'diklat'", $helper);
        $this->assertStringContainsString("'diklat' => 'diklat'", $helper);
        $this->assertStringContainsString('data-testid="profile-active-tab"', $view);
        $this->assertStringContainsString('Pangkat Terakhir', $helper);
        $this->assertStringContainsString('Jabatan Aktif', $helper);
        $this->assertStringContainsString('Unit Kerja', $helper);
        $this->assertStringContainsString('Status Kepegawaian', $helper);
        $this->assertStringContainsString('Belum ada data', $helper);
        $this->assertStringContainsString('@click="tab =', $view);
        $this->assertStringContainsString('Data hukuman belum ditampilkan di halaman ini.', $helper);
        $this->assertStringContainsString('Data penghargaan belum ditampilkan di halaman ini.', $helper);
        $this->assertStringContainsString('Data cuti belum ditampilkan di halaman ini.', $helper);
        $this->assertStringContainsString('Ringkasan', $helper);
        $this->assertStringContainsString('Data Pribadi', $helper);
        $this->assertStringContainsString('Riwayat Kepegawaian', $helper);
        $this->assertStringNotContainsString("tab === 'artis'", $view);
        $this->assertStringNotContainsString("'id' => 'artis'", $helper);
        $this->assertStringNotContainsString("'label' => 'Profile'", $helper);
        $this->assertStringNotContainsString("'label' => 'Or.Tu'", $helper);
    }

    public function test_profile_saya_defaults_to_profil_tab(): void
    {
        $this->skipIfDatabaseUnavailable();

        $user = $this->createPegawaiUser();

        $response = $this->actingAs($user)->get('/profile_saya');

        $response->assertOk();
        $response->assertSee('data-testid="profile-active-tab"', false);
        $response->assertSee('profil');
        $response->assertSee('Profile Pegawai');
        $response->assertSee('Data Kepegawaian');
        $response->assertSee('Pangkat Terakhir');
        $response->assertSee('Jabatan Aktif');
        $response->assertSee('Unit Kerja');
        $response->assertSee('Status Kepegawaian');
    }

    public function test_profile_saya_accepts_diklat_tab(): void
    {
        $this->skipIfDatabaseUnavailable();

        $user = $this->createPegawaiUser();

        $response = $this->actingAs($user)->get('/profile_saya?tab=diklat');

        $response->assertOk();
        $response->assertSee('data-testid="profile-active-tab"', false);
        $response->assertSee('diklat');
        $response->assertSee('Diklat Resmi');
        $response->assertSee('Seminar / Workshop');
        $response->assertSee('Latihan Jabatan');
    }

    public function test_profile_saya_legacy_artis_tab_alias_opens_diklat(): void
    {
        $this->skipIfDatabaseUnavailable();

        $user = $this->createPegawaiUser();

        $response = $this->actingAs($user)->get('/profile_saya?tab=artis');

        $response->assertOk();
        $response->assertSee('data-testid="profile-active-tab"', false);
        $response->assertSee('diklat');
    }

    public function test_edit_profile_form_preserves_contract_and_section_grouping(): void
    {
        $view = file_get_contents(resource_path('views/pages/dashboard/profile_pegawai/editProfilePegawai.blade.php'));

        $this->assertStringContainsString("action=\"{{ route('profile.pegawai.update') }}\"", $view);
        $this->assertStringContainsString('method="POST"', $view);
        $this->assertStringContainsString("@method('PUT')", $view);
        $this->assertStringContainsString('enctype="multipart/form-data"', $view);
        $this->assertStringContainsString('previewImage(event)', $view);
        $this->assertStringContainsString("getElementById('preview-foto')", $view);
        $this->assertStringContainsString('id="preview-foto" class="w-32 h-36', $view);
        $this->assertStringNotContainsString('<img id="preview-foto"', $view);

        foreach ([
            'Foto Profil',
            'Identitas Dasar',
            'Kontak',
            'Administrasi Kepegawaian',
            'Dokumen / SK',
            'Alamat',
        ] as $section) {
            $this->assertStringContainsString($section, $view);
        }

        foreach ([
            'foto',
            'nip',
            'nama',
            'unit_kerja_id',
            'gelar',
            'tmpt_lahir',
            'tgl_lahir',
            'jenis_kelamin',
            'agama',
            'golongan_darah',
            'status_pernikahan',
            'nik',
            'no_hp',
            'email',
            'email_gov',
            'no_npwp',
            'no_bpjs',
            'status_kepegawaian',
            'karpeg',
            'no_sk_cpns',
            'tmt_cpns',
            'no_sk_pns',
            'tmt_pns',
            'gol_awal',
            'nilai_tpp',
            'alamat',
        ] as $field) {
            $this->assertStringContainsString('name="' . $field . '"', $view);
        }
    }

    public function test_pegawai_sidebar_dead_non_pegawai_block_removed(): void
    {
        $view = file_get_contents(resource_path('views/components/app/sidebar.blade.php'));
        $pegawaiBranchStart = strpos($view, "auth()->user()->role === 'pegawai'");

        $this->assertNotFalse($pegawaiBranchStart);
        $this->assertStringNotContainsString("auth()->user()->role !== 'pegawai'", substr($view, $pegawaiBranchStart));
        $this->assertStringContainsString('Profile Saya', substr($view, $pegawaiBranchStart));
    }

    private function skipIfDatabaseUnavailable(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $exception) {
            $this->markTestSkipped('Database unavailable for runtime profile UI test: ' . $exception->getMessage());
        }
    }

    private function createPegawaiUser(): User
    {
        $user = User::factory()->create(['role' => 'pegawai']);

        Pegawai::factory()->create([
            'user_id' => $user->id,
            'nama' => 'Pegawai Runtime',
            'nip' => '199001012020011001',
            'email' => 'pegawai.runtime@example.test',
            'email_gov' => 'pegawai.runtime@gov.test',
        ]);

        return $user;
    }
}
