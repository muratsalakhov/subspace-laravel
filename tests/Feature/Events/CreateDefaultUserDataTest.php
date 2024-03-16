<?php

namespace Tests\Feature\Events;

use App\Events\HabitCreated;
use App\Events\TimetableCreated;
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

    public function test_handle_creates_default_data_for_new_user(): void
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

        // проверка, что для пользователя созданы расписания на каждый день недели
        $timetablesCount = Timetable::where('user_id', $user->id)->count();
        $this->assertEquals(7, $timetablesCount);

        // проверка, что флаг инициализации пользователя установлен в true
        $this->assertTrue($user->fresh()->is_initialized);
    }

    public function test_handle_does_not_create_data_if_user_already_initialized(): void
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
