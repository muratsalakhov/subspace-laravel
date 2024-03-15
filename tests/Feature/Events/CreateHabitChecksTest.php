<?php

namespace Tests\Feature\Events;

use App\Events\HabitCreated;
use App\Listeners\CreateHabitChecks;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateHabitChecksTest extends TestCase
{
    use DatabaseTransactions;

    public function test_habit_created_event_triggers_correct_action(): void
    {
        // подготовка
        Event::fake();

        $user = User::factory()->create();

        $habit = Habit::create([
            'user_id' => $user->id,
            'name' => 'Test Habit',
        ]);

        // действие
        $event = new HabitCreated($habit);
        $listener = new CreateHabitChecks();
        $listener->handle($event); // явно вызываем событие, чтобы проверить его результат

        // проверка
        $habit->refresh();
        $this->assertCount(Habit::HABIT_CHECKS_COUNT, $habit->checks);
    }

    public function test_habit_created_event_triggered_by_entity_creation(): void
    {
        // не используем Event::fake() и не вызываем событие явно
        // т.к. проверяем работу событий как есть

        // подготовка
        $user = User::factory()->create();

        // действие
        $habit = Habit::create([
            'user_id' => $user->id,
            'name' => 'Test Habit',
        ]);

        $habit->refresh();

        // проверка
        $this->assertCount(Habit::HABIT_CHECKS_COUNT, $habit->checks);
    }
}
