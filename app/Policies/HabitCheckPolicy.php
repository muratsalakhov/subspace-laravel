<?php

namespace App\Policies;

use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class HabitCheckPolicy
{
    use HandlesAuthorization;

    public function updateCheck(User $user, HabitCheck $habitCheck, Habit $habit): Response
    {
        if ($habitCheck->habit_id !== $habit->id) {
            return $this->deny('Галочка не принадлежит привычке');
        }

        if ($habit->user_id !== $user->id) {
            return $this->deny('Привычка не принадлежит пользователю');
        }

        return $this->allow();
    }
}
