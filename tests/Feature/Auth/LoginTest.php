<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_login_successfully(): void
    {
        // подготовка
        $email = 'login@example.com';
        $password = 'password';

        User::factory()->create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        // действие
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        // проверка
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email']
                ]
            ]);
    }
}
