<?php

namespace App\Http\Controllers;

use App\DTO\Auth\UserRegisterDTO;
use App\Exceptions\Auth\UserAlreadyExistsException;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use App\Services\Auth\RegistrationService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function __construct(
        private readonly AuthService $authService,
        private readonly RegistrationService $registrationService
    ) {
    }

    public function login(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->authService->login($validatedData['email'], $validatedData['password']);

        if (!$user) {
            return ApiResponse::error(null, 'Неверный email или пароль', 401);
        }

        return $this->getAuthResponse($user);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return ApiResponse::error(null, 'Пользователь не авторизован', 401);
        }

        $this->authService->logout($user);

        return ApiResponse::success([], 'Пользователь вышел из системы');
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        $user = $this->registrationService->register(
            new UserRegisterDTO(
                name: $validatedData['name'],
                email: $validatedData['email'],
                password: $validatedData['password']
            )
        );

        Auth::login($user);

        return $this->getAuthResponse($user);
    }

    private function getAuthResponse($user): JsonResponse
    {
        return ApiResponse::success([
            'token' => $this->authService->createToken($user)->plainTextToken,
            'user' => UserResource::make($user)
        ]);
    }
}
