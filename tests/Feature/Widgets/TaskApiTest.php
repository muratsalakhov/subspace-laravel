<?php

namespace Tests\Feature\Widgets;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_tasks_for_authenticated_user(): void
    {
        // подготовка
        $user = User::factory()->create();
        $user->tasks()->createMany([
            ['name' => 'Task 1', 'is_completed' => false],
            ['name' => 'Task 2', 'is_completed' => false],
        ]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson('/api/v1/tasks');

        // проверка
        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Task 1')
            ->assertJsonPath('data.1.name', 'Task 2');
    }

    public function test_show_returns_task(): void
    {
        // подготовка
        $user = User::factory()->create();
        $task = $user->tasks()->create(['name' => 'Task 1', 'is_completed' => false]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Task 1');
    }

    public function test_store_creates_new_task(): void
    {
        // подготовка
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        // действие
        $response = $this->postJson('/api/v1/tasks', ['name' => 'New Task']);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'New Task');
        $this->assertDatabaseHas('tasks', ['name' => 'New Task']);
    }

    public function test_store_requires_name(): void
    {
        // подготовка
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // действие
        $response = $this->postJson('/api/v1/tasks', []);

        // проверка
        $response->assertStatus(422);
    }

    public function test_update_modifies_task(): void
    {
        // подготовка
        $user = User::factory()->create();
        $task = $user->tasks()->create(['name' => 'Old Task', 'is_completed' => false]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->putJson("/api/v1/tasks/{$task->id}", ['name' => 'Updated Task']);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Task');
        $this->assertDatabaseHas('tasks', ['name' => 'Updated Task']);
    }

    public function test_destroy_deletes_task(): void
    {
        // подготовка
        $user = User::factory()->create();
        $task = $user->tasks()->create(['name' => 'Task to be deleted', 'is_completed' => false]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        // проверка
        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_update_status_updates_task_status(): void
    {
        // подготовка
        $user = User::factory()->create();
        $task = $user->tasks()->create(['name' => 'Task Status', 'is_completed' => false]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->patchJson("/api/v1/tasks/{$task->id}", ['is_completed' => true]);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.is_completed', true);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'is_completed' => true]);
    }

    public function test_update_status_does_not_change_other_task_fields(): void
    {
        // подготовка
        $user = User::factory()->create();
        $task = $user->tasks()->create(['name' => 'Task Status', 'is_completed' => false]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->patchJson("/api/v1/tasks/{$task->id}", ['is_completed' => true]);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.is_completed', true)
            ->assertJsonPath('data.name', 'Task Status');
    }

    public function test_user_cannot_access_others_tasks(): void
    {
        // подготовка
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();
        $task = $userTwo->tasks()->create(['name' => 'Task 1', 'is_completed' => false]);

        Sanctum::actingAs($userOne);

        // действие + проверка
        $response = $this->getJson("/api/v1/tasks/{$task->id}");
        $response->assertForbidden();

        $updateResponse = $this->putJson("/api/v1/tasks/{$task->id}", ['name' => 'Unauthorized Update']);
        $updateResponse->assertForbidden();
    }
}
