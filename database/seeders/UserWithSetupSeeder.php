<?php

namespace Database\Seeders;

use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\Note;
use App\Models\Task;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserWithSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* @var User $user */
        $user = User::factory()->create();

        $user->tasks()->saveMany(
            Task::factory(5)->make()
        );

        $user->notes()->saveMany(
            Note::factory(3)->make()
        );

        $habits = Habit::factory(5)->make();
        $user->habits()->saveMany($habits);
        /** @var Habit $habit */
        foreach ($habits as $habit) {
            $habit->checks()->saveMany(
                HabitCheck::factory(21)->make([
                    'is_completed' => false
                ])
            );
        }

        $timetables = Timetable::factory(7)->make();
        $user->timetables()->saveMany($timetables);
        /* @var Timetable $timetable */
        foreach ($timetables as $key => $timetable) {
            $timetable->day_of_week = $key + 1;

            $slots = TimetableSlot::factory(7)->make();
            /* @var TimetableSlot $slot */
            foreach ($slots as $slotKey => $slot) {
                $slot->slot_number = $slotKey + 1;
            }
            $timetable->slots()->saveMany($slots);
        }
    }
}
