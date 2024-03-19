<?php

namespace App\Policies;

use App\Models\Timetable;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TimetablePolicy
{

    use HandlesAuthorization;

    private const NOT_BELONG_MESSAGE = 'Расписание не принадлежит текущему пользователю';

    /**
     * Права на просмотр списка привычек
     */
    public function viewAny(User $user): Response
    {
        return $this->allow();
    }

    /**
     * Права на просмотр конкретной привычки
     */
    public function view(User $user, Timetable $timetable): Response
    {
        if ($user->id === $timetable->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }

    /**
     * Права на создание
     */
    public function create(User $user): Response
    {
        return $this->allow();
    }

    /**
     * Права на обновление
     */
    public function update(User $user, Timetable $timetable): Response
    {
        if ($user->id === $timetable->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }

    /**
     * Права на удаление
     */
    public function delete(User $user, Timetable $timetable): Response
    {
        if ($user->id === $timetable->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }
}
