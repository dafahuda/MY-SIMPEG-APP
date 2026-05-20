<?php

namespace Tests\Feature\Diklat;

use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DiklatSubmissionAuthorizationTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_submission_routes_are_registered_with_expected_paths(): void
    {
        $this->assertTrue(Route::has('diklat_saya.index'));
        $this->assertTrue(Route::has('diklat_saya.show'));
        $this->assertTrue(Route::has('diklat_saya.store'));
        $this->assertTrue(Route::has('diklat_saya.update'));
        $this->assertTrue(Route::has('diklat_verifikasi.index'));
        $this->assertTrue(Route::has('diklat_verifikasi.show'));
        $this->assertTrue(Route::has('diklat_verifikasi.approve'));
        $this->assertTrue(Route::has('diklat_verifikasi.reject'));

        $this->assertSame('/pegawai/diklat_saya', parse_url(route('diklat_saya.index'), PHP_URL_PATH));
        $this->assertSame('/pegawai/diklat_saya/123', parse_url(route('diklat_saya.show', 123), PHP_URL_PATH));
        $this->assertSame('/pegawai/diklat_saya/123', parse_url(route('diklat_saya.store', 123), PHP_URL_PATH));
        $this->assertSame('/pegawai/diklat_saya/123', parse_url(route('diklat_saya.update', 123), PHP_URL_PATH));

        $this->assertSame('/kepegawaian/diklat_verifikasi', parse_url(route('diklat_verifikasi.index'), PHP_URL_PATH));
        $this->assertSame('/kepegawaian/diklat_verifikasi/123', parse_url(route('diklat_verifikasi.show', 123), PHP_URL_PATH));
        $this->assertSame('/kepegawaian/diklat_verifikasi/123/approve', parse_url(route('diklat_verifikasi.approve', 123), PHP_URL_PATH));
        $this->assertSame('/kepegawaian/diklat_verifikasi/123/reject', parse_url(route('diklat_verifikasi.reject', 123), PHP_URL_PATH));
    }

    public function test_pegawai_can_access_self_service_skeleton_routes(): void
    {
        $fixture = $this->createSubmissionScopeFixture();
        $pegawai = $fixture['pegawaiInScope']->user;

        $this->actingAs($pegawai);

        $this->get(route('diklat_saya.index'))->assertOk()->assertSee('Diklat Saya');
        $this->get(route('diklat_saya.show', $fixture['rencanaInScope']->id))->assertOk()->assertSee('Rencana Pengajuan In Scope');
    }

    public function test_admin_and_superadmin_cannot_mutate_through_pegawai_self_service_routes(): void
    {
        foreach (['admin', 'superadmin'] as $role) {
            $this->actingAs($this->createUser($role, $role . '-self-service'));

            $this->post(route('diklat_saya.store', 999))->assertForbidden();
            $this->put(route('diklat_saya.update', 123))->assertForbidden();
        }
    }

    public function test_pegawai_cannot_access_verification_routes(): void
    {
        $pegawai = $this->createUser('pegawai', 'pegawai-verification');

        $this->actingAs($pegawai);

        $this->get(route('diklat_verifikasi.index'))->assertForbidden();
        $this->get(route('diklat_verifikasi.show', 123))->assertForbidden();
        $this->post(route('diklat_verifikasi.approve', 123))->assertForbidden();
        $this->post(route('diklat_verifikasi.reject', 123))->assertForbidden();
    }

    public function test_admin_and_superadmin_can_access_verification_routes(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['admin']);
        $this->get(route('diklat_verifikasi.index'))->assertOk()->assertSee('Verifikasi Pengajuan');
        $this->get(route('diklat_verifikasi.show', $fixture['submissionInScope']->id))->assertOk()->assertSee('Detail Verifikasi Pengajuan');
        $this->post(route('diklat_verifikasi.approve', $fixture['submissionInScope']->id))->assertRedirect(route('diklat_verifikasi.index'));

        $adminRejectRencana = $this->createAssignedSubmissionRencana($fixture['pegawaiInScope'], 'Rencana Admin Reject Route', '2029');
        $adminRejectSubmission = $this->createSubmission($adminRejectRencana, $fixture['pegawaiInScope'], \App\Models\PengajuanDiklat::STATUS_PENDING);
        $this->post(route('diklat_verifikasi.reject', $adminRejectSubmission->id))->assertRedirect(route('diklat_verifikasi.index'));

        $this->actingAs($fixture['superadmin']);
        $this->get(route('diklat_verifikasi.index'))->assertOk()->assertSee('Verifikasi Pengajuan');
        $this->get(route('diklat_verifikasi.show', $fixture['submissionOutOfScope']->id))->assertOk()->assertSee('Detail Verifikasi Pengajuan');
        $this->post(route('diklat_verifikasi.approve', $fixture['submissionOutOfScope']->id))->assertRedirect(route('diklat_verifikasi.index'));

        $superadminRejectPegawai = $this->createSubmissionPegawai('pegawai-superadmin-reject-route', $fixture['unitOutOfScope']);
        $superadminRejectRencana = $this->createAssignedSubmissionRencana($superadminRejectPegawai, 'Rencana Superadmin Reject Route', '2029');
        $superadminRejectSubmission = $this->createSubmission($superadminRejectRencana, $superadminRejectPegawai, \App\Models\PengajuanDiklat::STATUS_PENDING);
        $this->post(route('diklat_verifikasi.reject', $superadminRejectSubmission->id))->assertRedirect(route('diklat_verifikasi.index'));
    }

    public function test_submission_scope_fixture_distinguishes_in_unit_and_out_of_unit_submissions(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->assertSame($fixture['admin']->unit_kerja_id, $fixture['pegawaiInScope']->unit_kerja_id);
        $this->assertNotSame($fixture['admin']->unit_kerja_id, $fixture['pegawaiOutOfScope']->unit_kerja_id);
        $this->assertSame($fixture['pegawaiInScope']->id, $fixture['submissionInScope']->pegawai_id);
        $this->assertSame($fixture['pegawaiOutOfScope']->id, $fixture['submissionOutOfScope']->pegawai_id);
        $this->assertSame($fixture['rencanaInScope']->id, $fixture['submissionInScope']->rencana_diklat_id);
        $this->assertSame($fixture['rencanaOutOfScope']->id, $fixture['submissionOutOfScope']->rencana_diklat_id);
    }

    private function createUser(string $role, string $username): User
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit ' . $username,
            'alamat' => 'Jl. ' . $username,
        ]);

        return User::create([
            'username' => $username,
            'name' => 'User ' . $username,
            'email' => $username . '@example.test',
            'role' => $role,
            'unit_kerja_id' => $role === 'superadmin' ? null : $unitKerja->id,
            'password' => bcrypt('password'),
        ]);
    }
}
