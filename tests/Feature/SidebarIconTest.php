<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarIconTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_has_distinct_semantic_icons_for_each_section_and_submenu(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);
        $html = $this->actingAs($user)->get('/dashboard')->assertOk()->getContent();

        foreach (['layout-dashboard', 'users', 'building-cog', 'school', 'briefcase', 'report-analytics', 'database-export', 'history', 'gavel', 'calendar-event'] as $icon) {
            $this->assertStringContainsString('data-nav-icon="'.$icon.'"', $html, $icon);
        }
        $this->assertSame(56, substr_count($html, 'data-nav-icon='));
        $this->assertStringContainsString('aria-hidden="true"', $html);
        $this->assertStringContainsString('viewBox="0 0 24 24"', $html);
        $this->assertStringNotContainsString('data-nav-icon="not-found"', $html);
    }

    public function test_pegawai_sidebar_uses_contextual_icons_without_inheriting_admin_sections(): void
    {
        $user = User::factory()->create(['role' => 'pegawai']);
        \App\Models\Pegawai::factory()->create(['user_id' => $user->id]);
        $html = $this->actingAs($user)->get('/profile_saya')->assertOk()->getContent();

        foreach (['user-circle', 'users-group', 'school', 'briefcase', 'clipboard-check', 'language'] as $icon) {
            $this->assertStringContainsString('data-nav-icon="'.$icon.'"', $html, $icon);
        }
        $this->assertSame(24, substr_count($html, 'data-nav-icon='));
        $this->assertStringNotContainsString('data-nav-icon="database-export"', $html);
    }
}
