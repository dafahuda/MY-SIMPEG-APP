<?php

namespace Tests\Feature\AdminSuperadminUiux;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class SidebarRoleMatrixTest extends TestCase
{
    public function test_superadmin_sidebar_exposes_setup_user_system_and_operational_links(): void
    {
        $html = $this->renderSidebarForRole('superadmin');

        $this->assertSidebarContainsLinks($html, [
            '/dashboard',
            '/manajemen_setup/instansi_lembaga',
            '/manajemen_setup/sekretariat',
            '/manajemen_setup/opd_skpd_unitkerja',
            '/manajemen_setup/data_user_admin',
            '/manajemen_setup/data_user_pegawai',
            '/data_pegawai/pegawai',
            '/kepegawaian/diklat',
            '/tpp/input_tpp',
            '/tpp/laporan_bulanan',
            '/notifikasi_kgb/data_notifikasi_kgb',
            '/rekapitulasi/opd_skpd_unit_kerja',
            '/report/nominatif',
            '/backup_data',
        ]);

        $this->assertStringContainsString('Manajemen', $html);
        $this->assertStringContainsString('Setup', $html);
        $this->assertStringContainsString('User', $html);
        $this->assertStringContainsString('Admin', $html);
        $this->assertStringContainsString('User', $html);
        $this->assertStringContainsString('Pegawai', $html);
        $this->assertStringContainsString('Backup', $html);
        $this->assertStringContainsString('Database', $html);
        $this->assertStringContainsString('Tunjangan', $html, 'Current sidebar typo label is intentionally pinned until the repair task changes it.');
    }

    public function test_admin_sidebar_hides_superadmin_only_setup_links_but_keeps_operational_links(): void
    {
        $html = $this->renderSidebarForRole('admin');

        $this->assertSidebarMissingLinks($html, [
            '/manajemen_setup/instansi_lembaga',
            '/manajemen_setup/sekretariat',
            '/manajemen_setup/opd_skpd_unitkerja',
            '/manajemen_setup/data_user_admin',
        ]);

        $this->assertSidebarContainsLinks($html, [
            '/dashboard',
            '/manajemen_setup/data_user_pegawai',
            '/data_pegawai/pegawai',
            '/riwayat_keluarga/suami_istri',
            '/riwayat_pendidikan/sekolah',
            '/kepegawaian/jabatan',
            '/kepegawaian/diklat',
            '/tpp/input_tpp',
            '/notifikasi_kgb/data_notifikasi_kgb',
            '/report/nominatif',
            '/backup_data',
        ]);

        $this->assertStringContainsString('Manajemen', $html);
        $this->assertStringContainsString('Setup', $html);
        $this->assertStringContainsString('Data', $html);
        $this->assertStringContainsString('Pegawai', $html);
        $this->assertStringContainsString('Tunjangan', $html, 'Current sidebar typo label is intentionally pinned until the repair task changes it.');
        $this->assertStringNotContainsString('Instansi', $html);
        $this->assertStringNotContainsString('Sekretariat', $html);
    }

    public function test_pegawai_sidebar_hides_admin_and_superadmin_sections(): void
    {
        $html = $this->renderSidebarForRole('pegawai');

        $this->assertSidebarContainsLinks($html, [
            route('profile.pegawai'),
            '/riwayat_keluarga/suami_istri',
            '/riwayat_keluarga/anak',
            '/riwayat_pendidikan/sekolah',
            '/skp_prestasi_kerja/data_prestasi_kerja',
        ]);

        $this->assertSidebarMissingLinks($html, [
            '/dashboard',
            '/manajemen_setup',
            '/manajemen_setup/instansi_lembaga',
            '/manajemen_setup/data_user_admin',
            '/manajemen_setup/data_user_pegawai',
            '/data_pegawai/pegawai',
            '/kepegawaian/diklat',
            '/tpp/input_tpp',
            '/notifikasi_kgb/data_notifikasi_kgb',
            '/rekapitulasi/opd_skpd_unit_kerja',
            '/report/nominatif',
            '/backup_data',
        ]);

        $this->assertStringContainsString('Profile', $html);
        $this->assertStringContainsString('Saya', $html);
        $this->assertStringContainsString('Layanan Pegawai', $html);
        $this->assertStringNotContainsString('Manajemen', $html);
        $this->assertStringNotContainsString('Kepegawaian', $html);
        $this->assertStringNotContainsString('TPP', $html);
        $this->assertStringNotContainsString('Report', $html);
        $this->assertStringNotContainsString('Tunjangan', $html);
    }

    private function renderSidebarForRole(string $role): string
    {
        $this->actingAs(new User([
            'id' => 9001,
            'name' => ucfirst($role) . ' Sidebar Matrix',
            'email' => $role . '.sidebar@example.test',
            'username' => $role . '-sidebar-matrix',
            'role' => $role,
        ]));

        return Blade::render('<x-app.sidebar variant="v2" />');
    }

    private function assertSidebarContainsLinks(string $html, array $links): void
    {
        foreach ($links as $link) {
            $this->assertStringContainsString('href="' . e($link) . '"', $html, 'Missing sidebar link: ' . $link);
        }
    }

    private function assertSidebarMissingLinks(string $html, array $links): void
    {
        foreach ($links as $link) {
            $this->assertStringNotContainsString('href="' . e($link) . '"', $html, 'Unexpected sidebar link: ' . $link);
        }
    }
}
