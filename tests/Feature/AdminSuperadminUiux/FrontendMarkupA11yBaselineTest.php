<?php

namespace Tests\Feature\AdminSuperadminUiux;

use Tests\TestCase;

class FrontendMarkupA11yBaselineTest extends TestCase
{
    public function test_sidebar_logo_markup_has_valid_anchor_and_svg_contract(): void
    {
        $sidebar = file_get_contents(resource_path('views/components/app/sidebar.blade.php'));

        $this->assertStringContainsString('<a class="block" href="/" aria-label="SIMPEG App">', $sidebar);
        $this->assertStringContainsString('<svg class="fill-violet-500"', $sidebar);
        $this->assertStringContainsString('viewBox="0 0 32 32"', $sidebar);
        $this->assertStringNotContainsString(
            '<a class="block" href="/               <svg',
            $sidebar,
            'The SVG must remain child markup, not malformed href attribute text.'
        );
    }

    public function test_layout_asset_paths_use_asset_helper_and_confirm_modal_mount_remains(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));
        $authLayout = file_get_contents(resource_path('views/layouts/authentication.blade.php'));

        $this->assertStringContainsString("href=\"{{ asset('images/logo_asn.png') }}\"", $layout);
        $this->assertStringContainsString("href=\"{{ asset('images/logo_asn.png') }}\"", $authLayout);
        $this->assertStringContainsString("src=\"{{ asset('images/logo_asn.png') }}\"", $authLayout);
        $this->assertStringContainsString('alt="Logo ASN"', $authLayout);
        $this->assertStringNotContainsString('href="images/logo_asn.png"', $layout);
        $this->assertStringNotContainsString('href="images/logo_asn.png"', $authLayout);
        $this->assertStringNotContainsString('src="images/logo_asn.png"', $authLayout);
        $this->assertStringContainsString('<x-app.confirm-modal />', $layout);
        $this->assertStringContainsString("document.addEventListener('click', function(e)", $layout);
        $this->assertStringContainsString("e.target.closest('.confirm-delete')", $layout);
    }

    public function test_confirm_modal_has_accessible_dialog_and_focus_hooks(): void
    {
        $modal = file_get_contents(resource_path('views/components/app/confirm-modal.blade.php'));

        $this->assertStringContainsString('x-data="confirmModalState()"', $modal);
        $this->assertStringContainsString('@open-confirm.window="show($event.detail)"', $modal);
        $this->assertStringContainsString('@keydown.escape.window="cancel()"', $modal);
        $this->assertStringContainsString('@keydown.tab="trapFocus($event)"', $modal);
        $this->assertStringContainsString('x-ref="dialog"', $modal);
        $this->assertStringContainsString('role="dialog"', $modal);
        $this->assertStringContainsString('aria-modal="true"', $modal);
        $this->assertStringContainsString('aria-labelledby="confirm-modal-title"', $modal);
        $this->assertStringContainsString('aria-describedby="confirm-modal-description"', $modal);
        $this->assertStringContainsString('id="confirm-modal-title"', $modal);
        $this->assertStringContainsString('id="confirm-modal-description"', $modal);
        $this->assertStringContainsString('x-ref="cancelButton"', $modal);
        $this->assertStringContainsString('_lastFocusedElement', $modal);
        $this->assertStringContainsString('trapFocus(event)', $modal);
    }

    public function test_global_confirm_handler_null_checks_forms_and_uses_request_submit(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('if (! href) return;', $layout);
        $this->assertStringContainsString('if (! form) return;', $layout);
        $this->assertStringContainsString("typeof form.requestSubmit === 'function'", $layout);
        $this->assertStringContainsString('form.requestSubmit();', $layout);
        $this->assertStringContainsString('form.submit();', $layout);
        $this->assertStringContainsString("e.target.closest('.confirm-delete')", $layout);
        $this->assertStringContainsString("e.target.closest('.confirm-save')", $layout);
    }

    public function test_audited_target_blank_links_include_rel_noopener_noreferrer(): void
    {
        $offenders = $this->targetBlankViewsMissingRel([
            'auth/register.blade.php',
            'components/dashboard/dashboard-card-05.blade.php',
            'pages/dashboard/kepegawaian/cuti/indexCuti.blade.php',
            'pages/dashboard/profile_pegawai/indexProfilePegawai.blade.php',
            'pages/dashboard/report/bezetting.blade.php',
            'pages/dashboard/report/duk.blade.php',
            'pages/dashboard/report/keadaan_pegawai.blade.php',
            'pages/dashboard/report/nominatif.blade.php',
        ]);

        $this->assertSame([], $offenders, 'Audited target=_blank links must include rel="noopener noreferrer".');
    }

    public function test_touched_informative_images_have_meaningful_alt_text(): void
    {
        $profile = file_get_contents(resource_path('views/pages/dashboard/profile_pegawai/indexProfilePegawai.blade.php'));
        $authLayout = file_get_contents(resource_path('views/layouts/authentication.blade.php'));

        $this->assertStringContainsString('alt="Foto {{ $pegawai->nama }}"', $profile);
        $this->assertStringContainsString('alt="Logo ASN"', $authLayout);
        $this->assertStringContainsString('alt="Ilustrasi aparatur sipil negara pada halaman autentikasi"', $authLayout);
        $this->assertStringNotContainsString('alt="Foto" class="w-full h-full object-cover"', $profile);
        $this->assertStringNotContainsString('alt="Authentication image"', $authLayout);
    }

    private function targetBlankViewsMissingRel(array $relativePaths): array
    {
        $viewsPath = resource_path('views');
        $offenders = [];

        foreach ($relativePaths as $relativePath) {
            $contents = file_get_contents($viewsPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));

            if (! preg_match_all('/<a\b[^>]*target\s*=\s*["\']_blank["\'][^>]*>/is', $contents, $matches)) {
                continue;
            }

            foreach ($matches[0] as $anchor) {
                if (! preg_match('/\brel\s*=\s*["\'][^"\']*\bnoopener\b[^"\']*\bnoreferrer\b/i', $anchor)) {
                    $offenders[] = $relativePath;
                    break;
                }
            }
        }

        sort($offenders);

        return $offenders;
    }
}
