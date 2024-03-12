<?php

namespace Tests\Feature\Events;

use App\Listeners\CreateDefaultUserData;
use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateDefaultUserDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testHandleCreatesDefaultDataForNewUser(): void
    {
        // подготовка
        Event::fake();

        $user = User::factory()->create(['is_initialized' => false]);

        // действие
        $event = new Registered($user);
        $listener = new CreateDefaultUserData();
        $listener->handle($event);

        // проверки, что начальные данные были созданы именно для этого пользователя
        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'title' => 'Первая заметка',
        ]);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'name' => 'Первая задача',
        ]);

        // проверка наличия 3 привычек для этого пользователя
        $habits = Habit::where('user_id', $user->id)->get();
        $this->assertCount(3, $habits);

        // для каждой привычки должно быть 18 проверок
        $habits = Habit::where('user_id', $user->id)->get();
        foreach ($habits as $habit) {
            $checksCount = HabitCheck::where('habit_id', $habit->id)->count();
            $this->assertEquals(18, $checksCount);
        }

        // проверка, что для пользователя созданы расписания на каждый день недели
        $timetablesCount = Timetable::where('user_id', $user->id)->count();
        $this->assertEquals(7, $timetablesCount);

        // проверка, что для каждого расписания создано по 7 слотов
        $timetables = Timetable::where('user_id', $user->id)->get();
        foreach ($timetables as $timetable) {
            $slotsCount = TimetableSlot::where('timetable_id', $timetable->id)->count();
            $this->assertEquals(7, $slotsCount);
        }

        // проверка, что флаг инициализации пользователя установлен в true
        $this->assertTrue($user->fresh()->is_initialized);
    }

    public function testHandleDoesNotCreateDataIfUserAlreadyInitialized(): void
    {
        // подготовка
        Event::fake();

        $user = User::factory()->make(['is_initialized' => true]);
        $user->save();

        // действие
        $event = new Registered($user);
        $listener = new CreateDefaultUserData();
        $listener->handle($event);

        // проверка
        $this->assertDatabaseMissing('notes', [
            'user_id' => $user->id,
            'title' => 'Первая заметка',
        ]);
    }
}
