<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny');

        return ApiResponse::success([]);
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return ApiResponse::success([]);
    }

    public function store(): JsonResponse
    {
        $this->authorize('create');

        return ApiResponse::success([]);
    }

    public function update(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        return ApiResponse::success([]);
    }

    public function updateStatus(Task $task): JsonResponse
    {
        $this->authorize('updateStatus', $task);

        return ApiResponse::success([]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        return ApiResponse::success([]);
    }
}
