<?php

namespace Tests\Feature\Diklat;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatVerificationNavigationTest extends TestCase
{
    use DiklatSubmissionFixtures;
    use RefreshDatabase;

    public function test_admin_and_superadmin_see_verification_sidebar_link(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['admin'])
            ->get(route('diklat_verifikasi.index'))
            ->assertOk()
            ->assertSeeHtml('data-testid="sidebar-diklat-verifikasi-link"')
            ->assertSeeHtml('href="' . route('diklat_verifikasi.index') . '"')
            ->assertSeeHtml('data-testid="sidebar-rencana-diklat-link"')
            ->assertSeeHtml('href="' . route('rencana_diklat.index') . '"')
            ->assertSeeHtml('href="/kepegawaian/diklat"')
            ->assertSee('Alur Diklat')
            ->assertSee('Rencana Diklat')
            ->assertSee('Verifikasi Pengajuan')
            ->assertSee('Data Diklat Resmi');

        $this->actingAs($fixture['superadmin'])
            ->get(route('diklat_verifikasi.index'))
            ->assertOk()
            ->assertSeeHtml('data-testid="sidebar-diklat-verifikasi-link"')
            ->assertSeeHtml('href="' . route('diklat_verifikasi.index') . '"')
            ->assertSee('Alur Diklat')
            ->assertSee('Rencana Diklat')
            ->assertSee('Verifikasi Pengajuan')
            ->assertSee('Data Diklat Resmi');
    }

    public function test_pegawai_sees_diklat_saya_sidebar_link_without_verification_link(): void
    {
        $fixture = $this->createSubmissionScopeFixture();

        $this->actingAs($fixture['pegawaiInScope']->user)
            ->get(route('diklat_saya.index'))
            ->assertOk()
            ->assertSeeHtml('data-testid="sidebar-diklat-saya-link"')
            ->assertSeeHtml('href="' . route('diklat_saya.index') . '"')
            ->assertSeeHtml('href="' . route('profile.pegawai') . '"')
            ->assertDontSeeHtml('href="' . route('profile.pegawai', ['tab' => 'diklat']) . '"')
            ->assertSee('Diklat Saya')
            ->assertSee('Profile Saya')
            ->assertSee('Layanan Pegawai')
            ->assertDontSee('Profil & Diklat Saya')
            ->assertDontSeeHtml('data-testid="sidebar-diklat-verifikasi-link"')
            ->assertDontSeeHtml('data-testid="sidebar-rencana-diklat-link"')
            ->assertDontSeeHtml('href="/kepegawaian/diklat"')
            ->assertDontSee('Alur Diklat')
            ->assertDontSee('Verifikasi Pengajuan')
            ->assertDontSee('Data Diklat Resmi');
    }
}
