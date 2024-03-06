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
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly RegistrationService $registrationService
    ) {
    }

    #[
        Group('Регистрация/авторизация'),
        Endpoint('Авторизация', 'Авторизовать пользователя'),
        BodyParam('email', description: 'Email, указанный при регистрации', example: 'newuser@example.com'),
        BodyParam('password', description: 'Пароль, указанный при регистрации', example: 'newsecret'),
        Response(content: '{"token": "token", "user": {"id": 1, "name": "Иван Новый", "email": "newuser@example.com"}}', status: 200, description: "Успешная авторизация")
    ]
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

    #[
        Group('Регистрация/авторизация'),
        Endpoint('Выход из системы', 'Разавторизовать пользователя'),
        Response(content: '{"message": "Пользователь вышел из системы"}', status: 200, description: 'Пользователь вышел из системы')
    ]
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
    #[
        Group('Регистрация/авторизация'),
        Endpoint('Регистрация', 'Зарегистрировать пользователя'),
        BodyParam('name', description: 'Максимальная длина 255 символов', example: 'Иван Новый'),
        BodyParam('email', description: 'Должен быть действительным электронным адресом, максимальная длина 255 символов, уникальный в таблице users', example: 'newuser@example.com'),
        BodyParam('password', description: 'Минимум 6 символов', example: 'newsecret'),
        Response(content: '{"token": "token", "user": {"id": 1, "name": "Иван Новый", "email": "newuser@example.com"}}', status: 200, description: "Успешная регистрация")
    ]
    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
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
