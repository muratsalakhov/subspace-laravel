<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        return ApiResponse::success([]);
    }

    public function logout(Request $request): JsonResponse
    {
        return ApiResponse::success([]);
    }

    public function register(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        // todo: вынести регистрацию в отдельный сервис
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password']
        ]);

        // todo: вынести авторизацию в отдельный сервис
        Auth::login($user);

        return ApiResponse::success([
            'token' => $user->createToken('default')->plainTextToken
        ]);
    }
}
