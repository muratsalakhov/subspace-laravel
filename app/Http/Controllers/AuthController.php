<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
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
        return ApiResponse::success([]);
    }
}
