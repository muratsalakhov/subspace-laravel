<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {

        return ApiResponse::success([]);
    }

    public function show(Task $task): JsonResponse
    {
        return ApiResponse::success([]);
    }

    public function store(): JsonResponse
    {
        return ApiResponse::success([]);
    }

    public function update(Task $task): JsonResponse
    {
        return ApiResponse::success([]);
    }

    public function updateStatus(Task $task): JsonResponse
    {
        return ApiResponse::success([]);
    }

    public function destroy(Task $task): JsonResponse
    {
        return ApiResponse::success([]);
    }
}
