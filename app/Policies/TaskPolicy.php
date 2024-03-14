<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    use HandlesAuthorization;

    private const NOT_BELONG_MESSAGE = 'Задача не принадлежит текущему пользователю';

    /**
     * Права на просмотр списка задач
     */
    public function viewAny(User $user): Response
    {
        return $this->allow();
    }

    /**
     * Права на просмотр конкретной задачи
     */
    public function view(User $user, Task $task): Response
    {
        if ($user->id === $task->user_id) {
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
    public function update(User $user, Task $task): Response
    {
        if ($user->id === $task->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }

    /**
     * Права на обновление статуса
     * @param User $user
     * @param Task $task
     * @return Response
     */
    public function updateStatus(User $user, Task $task): Response {
        if ($user->id === $task->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }

    /**
     * Права на удаление
     */
    public function delete(User $user, Task $task): Response
    {
        if ($user->id === $task->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }
}
