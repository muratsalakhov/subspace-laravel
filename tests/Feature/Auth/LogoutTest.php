<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_logout_successfully(): void
    {
        // подготовка
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // действие
        $response = $this->postJson('/api/v1/auth/logout');

        // проверка
        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Пользователь вышел из системы']);
    }
}
