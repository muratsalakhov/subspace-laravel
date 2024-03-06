<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_register_successfully(): void
    {
        // подготовка + действие
        $email = 'test@example.com';
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password',
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

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }
}
