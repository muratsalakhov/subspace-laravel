<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    use RequiresAuthenticatedUser;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $user = $this->getUser();

        return ApiResponse::success(
            TaskResource::collection($user->tasks)
        );
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return ApiResponse::success(
            TaskResource::make($task)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $user = $this->getUser();

        $task = $user->tasks()->create($validatedData);

        return ApiResponse::success(
            TaskResource::make($task)
        );
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validatedData = $this->validate($request, [
            'name' => 'sometimes|string|max:255',
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($validatedData);

        return ApiResponse::success(
            TaskResource::make($task)
        );
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $this->authorize('updateStatus', $task);

        $validatedData = $this->validate($request, [
            'is_completed' => 'required|boolean',
        ]);

        $task->update($validatedData);

        return ApiResponse::success(
            TaskResource::make($task)
        );
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return ApiResponse::success([], code: Response::HTTP_NO_CONTENT);
    }
}
