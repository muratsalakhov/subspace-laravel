<?php

namespace App\Listeners;

use App\Events\HabitCreated;
use App\Models\Habit;

class CreateHabitChecks
{
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
    public function handle(HabitCreated $event): void
    {
        $checks = [];
        for ($i = 0; $i < Habit::HABIT_CHECKS_COUNT; $i++) {
            $checks[] = [
                'is_completed' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        $event->habit->checks()->createMany($checks);
    }
}
