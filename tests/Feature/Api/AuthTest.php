<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi', 'email' => 'budi@example.com',
            'password' => 'password', 'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)->assertJsonStructure(['message', 'token', 'user']);
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
    }

    public function test_login_with_valid_credentials_returns_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email, 'password' => 'secret123',
        ]);

        $response->assertOk()->assertJsonStructure(['message', 'token', 'user']);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email, 'password' => 'wrong',
        ])->assertStatus(422);
    }

    public function test_me_requires_bearer_token(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_me_returns_current_user_with_valid_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $token = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email, 'password' => 'secret123',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_logout_invalidates_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $token = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email, 'password' => 'secret123',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout')->assertOk();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')->assertStatus(401);
    }
}
