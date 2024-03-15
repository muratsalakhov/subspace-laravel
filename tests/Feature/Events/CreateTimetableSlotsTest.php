<?php

namespace Tests\Feature\Events;

use App\Events\HabitCreated;
use App\Events\TimetableCreated;
use App\Listeners\CreateHabitChecks;
use App\Listeners\CreateTimetableSlots;
use App\Models\Habit;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateTimetableSlotsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_timetable_created_event_triggers_correct_action(): void
    {
        // подготовка
        Event::fake();

        $user = User::factory()->create();

        $timetable = Timetable::create([
            'user_id' => $user->id,
            'day_of_week' => 1
        ]);

        // действие
        $event = new TimetableCreated($timetable);
        $listener = new CreateTimetableSlots();
        $listener->handle($event); // явно вызываем событие, чтобы проверить его результат

        $timetable->refresh();

        // проверка
        $this->assertCount(Timetable::TIMETABLE_SLOTS_COUNT, $timetable->slots);
    }

    public function test_timetable_created_event_triggered_by_entity_creation(): void
    {
        // не используем Event::fake() и не вызываем событие явно
        // т.к. проверяем работу событий как есть

        // подготовка
        $user = User::factory()->create();

        // действие
        $timetable = Timetable::create([
            'user_id' => $user->id,
            'day_of_week' => 1
        ]);

        $timetable->refresh();

        // проверка
        $this->assertCount(Timetable::TIMETABLE_SLOTS_COUNT, $timetable->slots);
    }
}
