<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->asUser($user)->putJson('/api/auth/me', ['name' => 'New Name', 'email' => 'NEW@test.com'])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.email', 'new@test.com');
    }

    public function test_email_must_stay_unique(): void
    {
        User::factory()->create(['email' => 'taken@test.com']);

        $this->asUser(User::factory()->create())->putJson('/api/auth/me', ['email' => 'taken@test.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_can_be_changed(): void
    {
        $user = User::factory()->create(['email' => 'ann@test.com']);

        $this->asUser($user)->putJson('/api/auth/password', ['current_password' => 'password', 'new_password' => 'brand-new-pass'])
            ->assertNoContent();

        $this->app['auth']->forgetGuards();
        $this->postJson('/api/auth/login', ['email' => 'ann@test.com', 'password' => 'brand-new-pass'])->assertOk();
    }

    public function test_password_change_needs_the_current_password(): void
    {
        $this->asUser(User::factory()->create())
            ->putJson('/api/auth/password', ['current_password' => 'wrong', 'new_password' => 'brand-new-pass'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);
    }
}
