<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Задачи')]
class TaskController extends Controller
{
    use RequiresAuthenticatedUser;

    #[Endpoint('Список задач', 'Получить список всех задач пользователя')]
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $user = $this->getUser();

        return ApiResponse::success(
            TaskResource::collection($user->tasks)
        );
    }

    #[Endpoint('Просмотр задачи', 'Получить детальную информацию о задаче')]
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return ApiResponse::success(
            TaskResource::make($task)
        );
    }

    #[
        Endpoint('Создание задачи', 'Создать новую задачу'),
        BodyParam('name', 'string', 'Название задачи, макс 255 символов')
    ]
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

    #[
        Endpoint('Обновление задачи', 'Обновить данные задачи'),
        BodyParam('name', 'string', 'Название задачи, макс 255 символов'),
        BodyParam('is_completed', 'boolean', 'Является ли выполненной')
    ]
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

    #[
        Endpoint('Изменение статуса задачи', 'Обновить статус задачи'),
        BodyParam('is_completed', 'boolean', 'Является ли выполненной')
    ]
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

    #[Endpoint('Удаление задачи', 'Удалить задачу')]
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return ApiResponse::success([], code: Response::HTTP_NO_CONTENT);
    }
}
