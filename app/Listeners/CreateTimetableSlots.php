<?php

namespace App\Listeners;

use App\Events\TimetableCreated;
use App\Models\Timetable;
use App\Models\TimetableSlot;

class CreateTimetableSlots
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
    public function handle(TimetableCreated $event): void
    {
        $slots = [];
        for ($slot = 1; $slot <= Timetable::TIMETABLE_SLOTS_COUNT; $slot++) {
            $slots[] = [
                'slot_number' => $slot,
                'description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $event->timetable->slots()->createMany($slots);
    }
}
