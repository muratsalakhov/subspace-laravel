<?php

namespace App\Listeners;

use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\Note;
use App\Models\Task;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class CreateDefaultUserData
{
    private const HABITS_COUNT = 3;

    private const HABIT_CHECKS_COUNT = 18;

    private const TIMETABLE_SLOTS_COUNT = 7;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        /* @var User $user */
        $user = $event->user;

        // todo: transaction
        //      в случае ошибки полная отмена и дальнейшая обработка

        // заметки
        $this->createNotes($user);

        // список задач
        $this->createTasks($user);

        // привычки
        $this->createHabits($user);

        // расписание
        $this->createTimetables($user);
    }

    private function createNotes(User $user): void
    {
        Note::create([
            'user_id' => $user->id,
            'title' => 'Первая заметка',
            'body' => 'Это моя самая первая заметка!'
        ]);
    }

    private function createTasks(User $user): void
    {
        Task::create([
            'user_id' => $user->id,
            'name' => 'Первая задача',
            'is_completed' => false,
        ]);
        Task::create([
            'user_id' => $user->id,
            'name' => 'Вторая задача',
            'is_completed' => false,
        ]);
        Task::create([
            'user_id' => $user->id,
            'name' => 'Третья задача',
            'is_completed' => false,
        ]);
    }

    private function createHabits(User $user): void
    {
        for ($i = 1; $i <= self::HABITS_COUNT; $i++) {
            $habit = Habit::create([
                'user_id' => $user->id,
                'name' => "Привычка {$i}"
            ]);

            $habitChecks = [];
            for ($j = 1; $j <= self::HABIT_CHECKS_COUNT; $j++) {
                $habitChecks[] = [
                    'habit_id' => $habit->id,
                    'is_completed' => $j <= $i, // заполняем лесенкой 1 галочка для 1, 1 и 2 для 2 и т.д.
                ];
            }
            HabitCheck::insert($habitChecks);
        }
    }

    private function createTimetables(User $user): void
    {
        for ($day = 1; $day <= 7; $day++) { // неделя
            $timetableDay = Timetable::create([
                'user_id' => $user->id,
                'day_of_week' => $day
            ]);

            $timetableSlots = [];
            for ($slot = 1; $slot <= self::TIMETABLE_SLOTS_COUNT; $slot++) {
                $timetableSlots[] = [
                    'timetable_id' => $timetableDay->id,
                    'slot_number' => $slot,
                    'description' => ''
                ];
            }
            TimetableSlot::insert($timetableSlots);
        }
    }
}
