<?php

namespace App\Services\Auth;

use App\Models\User;
use Auth;
use Laravel\Sanctum\NewAccessToken;

/**
 * Сервис авторизации
 */
class AuthService
{
    /**
     * Авторизовать
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function login(string $email, string $password): ?User
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (Auth::attempt($credentials)) {
            return Auth::user();
        }

        return null;
    }

    /**
     * Разавторизовать
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Создать токен для пользователя
     * @param User $user
     * @return NewAccessToken
     */
    public function createToken(User $user): NewAccessToken
    {
        return $user->createToken('auth');
    }
}
