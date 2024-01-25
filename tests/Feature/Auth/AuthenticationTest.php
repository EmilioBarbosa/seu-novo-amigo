<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertUnauthorized();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $token = auth()->tokenById($user->id);

        $response = $this->postJson(
            route('logout'),
            headers: [
                "Authorization" => "Bearer $token"
            ]
        );

        $this->assertGuest();
        $response->assertSuccessful();
    }
}
