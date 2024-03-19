<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Resources\TimetableResource;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Symfony\Component\HttpFoundation\Response;

#[Group('Виджет "Расписание"')]
class TimetableController extends Controller
{
    use RequiresAuthenticatedUser;

    #[
        Endpoint('Список расписаний', 'Получить список всех расписаний пользователя'),
        ResponseFromApiResource(TimetableResource::class, Timetable::class, collection: true)
    ]
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Timetable::class);

        $user = $this->getUser();

        return ApiResponse::success(
            TimetableResource::collection($user->timetables)
        );
    }

    #[
        Endpoint('Просмотр расписания', 'Получить детальную информацию о расписании'),
        ResponseFromApiResource(TimetableResource::class, Timetable::class)
    ]
    public function show(Timetable $timetable): JsonResponse
    {
        $this->authorize('view', $timetable);

        return ApiResponse::success(
            TimetableResource::make($timetable)
        );
    }

    #[
        Endpoint('Создание расписания', 'Создать новое расписание'),
        BodyParam('day_of_week', 'string', 'Номер дня недели'),
        ResponseFromApiResource(TimetableResource::class, Timetable::class)
    ]
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Timetable::class);

        $validatedData = $this->validate($request, [
            'day_of_week' => 'required|integer|min:1|max:7',
        ]);

        $user = $this->getUser();

        $timetable = $user->timetables()->create($validatedData);

        return ApiResponse::success(
            TimetableResource::make($timetable)
        );
    }

    #[
        Endpoint('Обновление расписания', 'Обновить данные расписания'),
        BodyParam('day_of_week', 'string', 'Номер дня недели'),
        ResponseFromApiResource(TimetableResource::class, Timetable::class)
    ]
    public function update(Request $request, Timetable $timetable): JsonResponse
    {
        $this->authorize('update', $timetable);

        $validatedData = $this->validate($request, [
            'day_of_week' => 'required|integer|min:1|max:7',
        ]);

        $timetable->update($validatedData);

        return ApiResponse::success(
            TimetableResource::make($timetable)
        );
    }

    #[Endpoint('Удаление расписания', 'Удалить расписание')]
    public function destroy(Timetable $timetable): JsonResponse
    {
        $this->authorize('delete', $timetable);

        $timetable->delete();

        return ApiResponse::success([], code: Response::HTTP_NO_CONTENT);
    }

    #[
        Endpoint('Обновление слота расписания', 'Обновить данные слота'),
        BodyParam('slot_number', 'integer', 'Номер слота в расписании'),
        BodyParam('description', 'string ', 'Описание слота в расписании'),
        ResponseFromApiResource(TimetableResource::class, Timetable::class)
    ]
    public function updateSlot(Request $request, Timetable $timetable, TimetableSlot $slot): JsonResponse
    {
        $this->authorize('updateSlot', [$slot, $timetable]);

        $validatedData = $this->validate($request, [
            'slot_number' => 'required|integer',
            'description' => 'required|string'
        ]);

        $slot->update($validatedData);
        $timetable->refresh();

        return ApiResponse::success(
            TimetableResource::make($timetable)
        );
    }
}
