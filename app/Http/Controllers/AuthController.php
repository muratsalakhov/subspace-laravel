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
        return ApiResponse::success([]);
    }

    public function logout(Request $request): JsonResponse
    {
        return ApiResponse::success([]);
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

        return ApiResponse::success([
            'token' => $this->authService->createToken($user)->plainTextToken,
            'user' => UserResource::make($user)
        ]);
    }
}
