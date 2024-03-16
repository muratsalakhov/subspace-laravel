<?php

namespace App\Listeners;

use App\Models\Habit;
use App\Models\Note;
use App\Models\Task;
use App\Models\Timetable;
use App\Models\User;
use DB;
use Illuminate\Auth\Events\Registered;
use Log;

class CreateDefaultUserData
{
    private const HABITS_COUNT = 3;

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

        // выходим если пользователь уже был инициализирован
        if ($user->is_initialized) {
            return;
        }

        // начало транзакции
        DB::beginTransaction();

        try {
            // заметки
            $this->createNotes($user);

            // список задач
            $this->createTasks($user);

            // привычки
            $this->createHabits($user);

            // расписание
            $this->createTimetables($user);

            $user->is_initialized = true;
            $user->save();

            DB::commit();

            Log::info("Пользователь с id = {$user->id} успешно инициализирован");
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error(
                "Ошибка при инициализации пользователя с id = {$user->id}:
                {$exception->getMessage()} | {$exception->getTraceAsString()}"
            );
        }
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
            Habit::create([
                'user_id' => $user->id,
                'name' => "Привычка {$i}"
            ]);
        }
    }

    private function createTimetables(User $user): void
    {
        for ($day = 1; $day <= 7; $day++) { // неделя
            Timetable::create([
                'user_id' => $user->id,
                'day_of_week' => $day
            ]);
        }
    }
}
