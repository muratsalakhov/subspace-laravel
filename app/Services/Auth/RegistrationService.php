<?php

namespace App\Services\Auth;

use App\DTO\Auth\UserRegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Сервис регистрации
 */
class RegistrationService
{
    /**
     * Зарегистрировать пользователя
     * @param UserRegisterDTO $userRegisterDTO
     * @return User
     */
    public function register(UserRegisterDTO $userRegisterDTO): User
    {
        return User::create([
            'name' => $userRegisterDTO->name,
            'email' => $userRegisterDTO->email,
            'password' => Hash::make($userRegisterDTO->password)
        ]);
    }
}
