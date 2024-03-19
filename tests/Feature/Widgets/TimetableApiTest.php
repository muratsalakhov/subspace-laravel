<?php

namespace Tests\Feature\Widgets;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TimetableApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_timetables_for_authenticated_user(): void
    {
        // подготовка
        $user = User::factory()->create();
        $user->timetables()->createMany([
            ['day_of_week' => 1],
            ['day_of_week' => 2],
        ]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson('/api/v1/timetables');

        // проверка
        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_show_returns_timetable(): void
    {
        // подготовка
        $user = User::factory()->create();
        $timetable = $user->timetables()->create(['day_of_week' => 1]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson("/api/v1/timetables/{$timetable->id}");

        // проверка
        $response->assertOk()
            ->assertJsonPath('data.day_of_week', 1);
    }

    public function test_store_creates_new_timetable(): void
    {
        // подготовка
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        // действие
        $response = $this->postJson('/api/v1/timetables', ['day_of_week' => 3]);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.day_of_week', 3);
        $this->assertDatabaseHas('timetables', ['day_of_week' => 3]);
    }

    public function test_update_modifies_timetable(): void
    {
        // подготовка
        $user = User::factory()->create();
        $timetable = $user->timetables()->create(['day_of_week' => 1]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->patchJson("/api/v1/timetables/{$timetable->id}", ['day_of_week' => 2]);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.day_of_week', 2);
        $this->assertDatabaseHas('timetables', ['day_of_week' => 2]);
    }

    public function test_destroy_deletes_timetable(): void
    {
        // подготовка
        $user = User::factory()->create();
        $timetable = $user->timetables()->create(['day_of_week' => 1]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->deleteJson("/api/v1/timetables/{$timetable->id}");

        // проверка
        $response->assertNoContent();
        $this->assertDatabaseMissing('timetables', ['id' => $timetable->id]);
    }

    public function test_update_slot_modifies_timetable_slot(): void
    {
        // подготовка
        $user = User::factory()->create();
        $timetable = $user->timetables()->create(['day_of_week' => 1]);
        $slot = $timetable->slots()->create(['slot_number' => 1, 'description' => 'Morning Exercise']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->patchJson("/api/v1/timetables/{$timetable->id}/slots/{$slot->id}", [
            'slot_number' => 1,
            'description' => 'Updated Morning Exercise'
        ]);

        // проверка
        $response->assertOk();
        $this->assertDatabaseHas('timetable_slots', ['description' => 'Updated Morning Exercise']);
    }
}
