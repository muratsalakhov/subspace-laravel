<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Трейт для контроллера с авторизованным пользователем
 */
trait RequiresAuthenticatedUser
{
    /**
     * @throws AuthorizationException
     */
    protected function getUser(): User
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthorizationException();
        }

        return $user;
    }
}
