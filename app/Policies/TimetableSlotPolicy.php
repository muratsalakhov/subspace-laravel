<?php

namespace App\Policies;

use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TimetableSlotPolicy
{
    use HandlesAuthorization;

    public function updateSlot(User $user, TimetableSlot $slot, Timetable $timetable): Response
    {
        if ($slot->timetable_id !== $timetable->id) {
            return $this->deny('Слот не принадлежит расписанию');
        }

        if ($timetable->user_id !== $user->id) {
            return $this->deny('Расписание не принадлежит пользователю');
        }

        return $this->allow();
    }
}
