<?php

namespace App\Services\Auth;

use App\DTO\Auth\UserRegisterDTO;
use App\Exceptions\Auth\UserAlreadyExistsException;
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
     * @throws UserAlreadyExistsException
     */
    public function register(UserRegisterDTO $userRegisterDTO): User
    {
        if (User::where('email', $userRegisterDTO->email)->exists()) {
            throw new UserAlreadyExistsException();
        }

        return User::create([
            'name' => $userRegisterDTO->name,
            'email' => $userRegisterDTO->email,
            'password' => Hash::make($userRegisterDTO->password)
        ]);
    }
}
