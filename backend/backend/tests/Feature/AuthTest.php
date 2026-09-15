<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->postJson('/api/auth/register', ['name' => 'Ann', 'email' => 'Ann@Test.com', 'password' => 'secret123'])
            ->assertCreated()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('user.email', 'ann@test.com')
            ->assertJsonPath('user.role', 'customer');

        $this->assertDatabaseHas('users', ['email' => 'ann@test.com']);
    }

    public function test_registration_is_validated(): void
    {
        $this->postJson('/api/auth/register', ['name' => '', 'email' => 'nope', 'password' => 'short'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'ann@test.com']);

        $this->postJson('/api/auth/register', ['name' => 'Ann', 'email' => 'ann@test.com', 'password' => 'secret123'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_log_in_and_read_profile(): void
    {
        $user = User::factory()->create(['email' => 'ann@test.com']);

        $token = $this->postJson('/api/auth/login', ['email' => 'ann@test.com', 'password' => 'password'])
            ->assertOk()
            ->json('access_token');

        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', 'ann@test.com');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'ann@test.com']);

        $this->postJson('/api/auth/login', ['email' => 'ann@test.com', 'password' => 'wrong-pass'])
            ->assertUnauthorized();
    }

    public function test_profile_requires_authentication(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    public function test_token_can_be_refreshed_and_old_token_stops_working(): void
    {
        $old = JWTAuth::fromUser(User::factory()->create());

        $new = $this->withToken($old)->postJson('/api/auth/refresh')
            ->assertOk()
            ->assertJsonStructure(['access_token'])
            ->json('access_token');

        $this->assertNotSame($old, $new);

        $this->app['auth']->forgetGuards();
        $this->withToken($old)->getJson('/api/auth/me')->assertUnauthorized();

        $this->app['auth']->forgetGuards();
        $this->withToken($new)->getJson('/api/auth/me')->assertOk();
    }

    public function test_logout_invalidates_the_token(): void
    {
        $token = JWTAuth::fromUser(User::factory()->create());

        $this->withToken($token)->postJson('/api/auth/logout')->assertNoContent();

        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/auth/me')->assertUnauthorized();
    }
}
