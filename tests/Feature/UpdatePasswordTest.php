<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_current_password_must_be_correct(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->from('/user/profile')->put(route('user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect('/user/profile')
            ->assertSessionHasErrors(['current_password' => __('The provided password does not match your current password.')], null, 'updatePassword');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_new_passwords_must_match(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->from('/user/profile')->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'wrong-password',
        ])->assertRedirect('/user/profile')
            ->assertSessionHasErrors(['password'], null, 'updatePassword');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_profile_page_does_not_serialize_password_form_state_into_livewire_snapshot(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/user/profile');

        $response->assertOk();
        $response->assertDontSee('&quot;current_password&quot;:', false);
        $response->assertDontSee('&quot;password&quot;:', false);
        $response->assertDontSee('&quot;password_confirmation&quot;:', false);
    }
}
