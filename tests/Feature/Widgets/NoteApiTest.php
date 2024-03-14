<?php

namespace Tests\Feature\Widgets;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_notes_for_authenticated_user(): void
    {
        // подготовка
        $user = User::factory()->create();
        $user->notes()->createMany([
            ['title' => 'Note 1', 'body' => 'Content 1'],
            ['title' => 'Note 2', 'body' => 'Content 2'],
        ]);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson('/api/v1/notes');

        // проверка
        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'Note 1')
            ->assertJsonPath('data.1.title', 'Note 2');
    }

    public function test_show_returns_note(): void
    {
        // подготовка
        $user = User::factory()->create();
        $note = $user->notes()->create(['title' => 'Note 1', 'body' => 'Content 1']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->getJson("/api/v1/notes/{$note->id}");

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Note 1');
    }

    public function test_store_creates_new_note(): void
    {
        // подготовка
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        // действие
        $response = $this->postJson('/api/v1/notes', ['title' => 'New Note', 'body' => 'New Content']);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'New Note');
        $this->assertDatabaseHas('notes', ['title' => 'New Note']);
    }

    public function test_store_requires_title_and_body(): void
    {
        // подготовка
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // действие
        $response = $this->postJson('/api/v1/notes', []);

        // проверка
        $response->assertStatus(422);
    }

    public function test_update_title_modifies_note(): void
    {
        // подготовка
        $user = User::factory()->create();
        $note = $user->notes()->create(['title' => 'Original Title', 'body' => 'Original Content']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->patchJson("/api/v1/notes/{$note->id}/title", ['title' => 'Updated Title']);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Updated Title']);
    }

    public function test_update_body_modifies_note(): void
    {
        // подготовка
        $user = User::factory()->create();
        $note = $user->notes()->create(['title' => 'Title', 'body' => 'Original Content']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->patchJson("/api/v1/notes/{$note->id}/body", ['body' => 'Updated Content']);

        // проверка
        $response
            ->assertOk()
            ->assertJsonPath('data.body', 'Updated Content');
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'body' => 'Updated Content']);
    }

    public function test_destroy_deletes_note(): void
    {
        // подготовка
        $user = User::factory()->create();
        $note = $user->notes()->create(['title' => 'Note to be deleted', 'body' => 'Content to be deleted']);

        Sanctum::actingAs($user);

        // действие
        $response = $this->deleteJson("/api/v1/notes/{$note->id}");

        // проверка
        $response->assertNoContent();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_user_cannot_access_others_notes(): void
    {
        // подготовка
        $userOne = User::factory()->create();
        $userTwo = User::factory()->create();
        $note = $userTwo->notes()->create(['title' => 'Note 1', 'body' => 'Content 1']);

        Sanctum::actingAs($userOne);

        // действие + проверка
        $response = $this->getJson("/api/v1/notes/{$note->id}");
        $response->assertForbidden();

        $updateResponse = $this->deleteJson(
            "/api/v1/notes/{$note->id}",
            ['title' => 'Unauthorized Update', 'body' => 'Unauthorized Content']
        );
        $updateResponse->assertForbidden();
    }
}
