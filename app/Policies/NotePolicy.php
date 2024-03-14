<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class NotePolicy
{
    use HandlesAuthorization;

    private const NOT_BELONG_MESSAGE = 'Заметка не принадлежит текущему пользователю';

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
    public function view(User $user, Note $note): Response
    {
        if ($user->id === $note->user_id) {
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
    public function update(User $user, Note $note): Response
    {
        if ($user->id === $note->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }

    /**
     * Права на удаление
     */
    public function delete(User $user, Note $note): Response
    {
        if ($user->id === $note->user_id) {
            return $this->allow();
        }

        return $this->deny(self::NOT_BELONG_MESSAGE);
    }
}
