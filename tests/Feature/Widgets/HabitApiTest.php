<?php

namespace Tests\Feature\Widgets;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HabitApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_habits_for_authenticated_user(): void
    {
        // подготовка
        $user = User::factory()->create();
        $user->habits()->createMany([
            ['name' => 'Habit 1'],
            ['name' => 'Habit 2'],
        ]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson('/api/v1/habits');

        // проверка
        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Habit 1')
            ->assertJsonPath('data.1.name', 'Habit 2');
    }

    public function test_show_returns_habit(): void
    {
        // подготовка
        $user = User::factory()->create();
        $habit = $user->habits()->create(['name' => 'Habit 1']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson("/api/v1/habits/{$habit->id}");

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Habit 1');
    }

    public function test_store_creates_new_habit(): void
    {
        // подготовка
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        // действие
        $response = $this->postJson('/api/v1/habits', ['name' => 'New Habit']);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'New Habit');
        $this->assertDatabaseHas('habits', ['name' => 'New Habit']);
    }

    public function test_update_modifies_habit(): void
    {
        // подготовка
        $user = User::factory()->create();
        $habit = $user->habits()->create(['name' => 'Old Habit']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->patchJson("/api/v1/habits/{$habit->id}", ['name' => 'Updated Habit']);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Habit');
        $this->assertDatabaseHas('habits', ['name' => 'Updated Habit']);
    }

    public function test_destroy_deletes_habit(): void
    {
        // подготовка
        $user = User::factory()->create();
        $habit = $user->habits()->create(['name' => 'Habit to be deleted']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->deleteJson("/api/v1/habits/{$habit->id}");

        // проверка
        $response->assertNoContent();
        $this->assertDatabaseMissing('habits', ['id' => $habit->id]);
    }

    public function test_update_check_modifies_habit_check_status(): void
    {
        // подгтовка
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $habit = $user->habits()->create(['name' => 'Daily Running']);
        $check = $habit->checks()->create(['is_completed' => false]);

        // действие
        $response = $this->patchJson("/api/v1/habits/{$habit->id}/checks/{$check->id}", ['is_completed' => true]);
        $check->refresh();

        // проверка
        $response->assertOk();
        $this->assertTrue($check->is_completed);
    }

    public function test_user_cannot_access_others_habits(): void
    {
        // подготовка
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();
        $habit = $userTwo->habits()->create(['name' => 'Habit 1']);

        Sanctum::actingAs($userOne);

        // действие + проверка
        $response = $this->getJson("/api/v1/habits/{$habit->id}");
        $response->assertForbidden();
    }
}
