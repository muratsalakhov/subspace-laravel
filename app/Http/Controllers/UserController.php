<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

class UserController extends Controller
{
    #[
        Group('Пользователь'),
        Endpoint('Данные пользователя', 'Получить данные авторизованного пользователя'),
        ResponseFromApiResource(UserResource::class, User::class)
    ]
    public function show(): JsonResponse
    {
        return ApiResponse::success(
            UserResource::make(Auth::user())
        );
    }
}
