<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_errors_are_exposed_safely_to_form_enhancement(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($user)->withSession(['errors' => (new \Illuminate\Support\ViewErrorBag)->put('default', new \Illuminate\Support\MessageBag(['nama' => ['Nama wajib diisi.']]))])
            ->get('/data_pegawai/view_form_tambah_data_pegawai')
            ->assertOk()->assertSee('id="validation-data"', false)->assertSee('Periksa kembali data yang diisi.');
    }

    public function test_employee_profile_does_not_duplicate_flash_success(): void
    {
        $user = User::factory()->create(['role' => 'pegawai']);
        \App\Models\Pegawai::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->withSession(['success' => 'Profil berhasil diperbarui.', 'error' => 'Profil gagal diperbarui.'])->get('/profile_saya')->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'Profil berhasil diperbarui.'));
        $this->assertSame(1, substr_count($response->getContent(), 'Profil gagal diperbarui.'));
    }

    public function test_session_messages_render_once_in_unified_container(): void
    {
        $user = User::factory()->create(['role' => 'superadmin']);
        $response = $this->actingAs($user)->withSession([
            'success' => 'Data berhasil disimpan.',
            'warning' => 'Periksa data.',
            'error' => '<img src=x onerror=alert(1)>',
        ])->get('/dashboard')->assertOk();
        $html = $response->getContent();
        $this->assertSame(1, substr_count($html, 'id="toast-container"'));
        $this->assertSame(1, substr_count($html, 'Data berhasil disimpan.'));
        $response->assertSee('data-flash-type="warning"', false);
        $response->assertSee('&lt;img src=x onerror=alert(1)&gt;', false);
        $response->assertDontSee('<img src=x onerror=alert(1)>', false);
    }
}
