<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Resources\HabitResource;
use App\Models\Habit;
use App\Models\HabitCheck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Symfony\Component\HttpFoundation\Response;

#[Group('Виджет "Привычки"')]
class HabitController extends Controller
{
    use RequiresAuthenticatedUser;

    #[
        Endpoint('Список привычек', 'Получить список всех привычек пользователя'),
        ResponseFromApiResource(HabitResource::class, Habit::class, collection: true)
    ]
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Habit::class);

        $user = $this->getUser();

        return ApiResponse::success(
            HabitResource::collection($user->habits)
        );
    }

    #[
        Endpoint('Просмотр привычки', 'Получить детальную информацию о привычке'),
        ResponseFromApiResource(HabitResource::class, Habit::class)
    ]
    public function show(Habit $habit): JsonResponse
    {
        $this->authorize('view', $habit);

        return ApiResponse::success(
            HabitResource::make($habit)
        );
    }

    #[
        Endpoint('Создание привычки', 'Создать новую привычку'),
        BodyParam('name', 'string', 'Название привычки, макс 255 символов'),
        ResponseFromApiResource(HabitResource::class, Habit::class)
    ]
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Habit::class);

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $user = $this->getUser();

        $habit = $user->habits()->create($validatedData);

        return ApiResponse::success(
            HabitResource::make($habit)
        );
    }

    #[
        Endpoint('Обновление привычки', 'Обновить данные привычки'),
        BodyParam('name', 'string', 'Название привычки, макс 255 символов'),
        ResponseFromApiResource(HabitResource::class, Habit::class)
    ]
    public function update(Request $request, Habit $habit): JsonResponse
    {
        $this->authorize('update', $habit);

        $validatedData = $this->validate($request, [
            'name' => 'sometimes|string|max:255'
        ]);

        $habit->update($validatedData);

        return ApiResponse::success(
            HabitResource::make($habit)
        );
    }

    #[Endpoint('Удаление привычки', 'Удалить привычку')]
    public function destroy(Habit $habit): JsonResponse
    {
        $this->authorize('delete', $habit);

        $habit->delete();

        return ApiResponse::success([], code: Response::HTTP_NO_CONTENT);
    }

    #[
        Endpoint('Обновление галочки', 'Обновить данные галочки'),
        BodyParam('is_completed', 'boolean', 'Статус галочки'),
        ResponseFromApiResource(HabitResource::class, Habit::class)
    ]
    public function updateCheck(Request $request, Habit $habit, HabitCheck $check): JsonResponse
    {
        $this->authorize('updateCheck', [$check, $habit]);

        $validatedData = $this->validate($request, [
           'is_completed' => 'required|boolean'
        ]);

        $check->update($validatedData);
        $habit->refresh();

        return ApiResponse::success(
            HabitResource::make($habit)
        );
    }
}
