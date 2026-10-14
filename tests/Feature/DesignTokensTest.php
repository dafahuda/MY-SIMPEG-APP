<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Locks the DESIGN.md light-theme tokens and the dark-mode guard.
 *
 * The gray scale is shared between both themes, so the light theme is remapped
 * through a `:root:not(.dark)` block. These assertions fail loudly if someone
 * edits the token values, drops the scope guard (which would leak the light
 * palette into dark mode), or reverts the compact radius scale.
 */
class DesignTokensTest extends TestCase
{
    private string $css;

    protected function setUp(): void
    {
        parent::setUp();

        $this->css = file_get_contents(resource_path('css/app.css'));
    }

    public function test_light_theme_tokens_match_design_md(): void
    {
        foreach ([
            '--color-gray-100: #eef5fc;',   // app background
            '--color-gray-50: #f7fafd;',    // card / table surface
            '--color-gray-200: #cad9e8;',   // component border
            '--color-gray-800: #0a1628;',   // primary text
            '--color-gray-500: #53657a;',   // secondary text
        ] as $token) {
            $this->assertStringContainsString($token, $this->css, "Missing DESIGN.md token: {$token}");
        }

        $this->assertStringContainsString('--color-blue-600: #064A9D;', $this->css, 'Brand primary blue drifted.');
    }

    public function test_light_theme_remap_is_scoped_so_dark_mode_is_untouched(): void
    {
        $this->assertStringContainsString(
            ':root:not(.dark) {',
            $this->css,
            'The light-theme remap must stay scoped to :root:not(.dark), otherwise the shared gray scale leaks into dark mode.'
        );

        // The shared @theme block must keep the original Tailwind grays that
        // every `dark:*` utility resolves against.
        $this->assertStringContainsString('--color-gray-800: #1f2937;', $this->css);
        $this->assertStringContainsString('--color-gray-900: #111827;', $this->css);
    }

    public function test_compact_radius_scale_is_defined(): void
    {
        foreach ([
            '--radius-sm: 0.25rem;',
            '--radius-md: 0.25rem;',
            '--radius-lg: 0.25rem;',
        ] as $token) {
            $this->assertStringContainsString($token, $this->css, "Missing compact radius token: {$token}");
        }
    }

    public function test_sidebar_uses_the_dedicated_surface_token(): void
    {
        $sidebar = file_get_contents(resource_path('views/components/app/sidebar.blade.php'));

        $this->assertStringContainsString('bg-[#F4F8FD] dark:bg-gray-800', $sidebar);
    }

    public function test_inter_font_is_loaded_on_app_layout(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('family=Inter', $layout);
        $this->assertStringContainsString('font-inter', $layout);
    }
}
