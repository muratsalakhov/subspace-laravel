<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponse;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Symfony\Component\HttpFoundation\Response;

#[Group('Виджет "Заметки"')]
class NoteController extends Controller
{
    use RequiresAuthenticatedUser;

    #[
        Endpoint('Список заметок', 'Получить список всех заметок пользователя'),
        ResponseFromApiResource(NoteResource::class, Note::class, collection: true)
    ]
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Note::class);

        $user = $this->getUser();

        return ApiResponse::success(
            NoteResource::collection($user->notes)
        );
    }

    #[
        Endpoint('Просмотр заметки', 'Получить детальную информацию о заметке'),
        ResponseFromApiResource(NoteResource::class, Note::class)
    ]
    public function show(Note $note): JsonResponse
    {
        $this->authorize('view', $note);

        return ApiResponse::success(
            NoteResource::make($note)
        );
    }

    #[
        Endpoint('Создание заметки', 'Создать новую заметку'),
        BodyParam('title', 'string', 'Название заметки, макс 255 символов'),
        BodyParam('body', 'string', 'Содержимое заметки'),
        ResponseFromApiResource(NoteResource::class, Note::class)
    ]
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Note::class);

        $validatedData = $this->validate($request, [
            'title' => 'required|string|max:255',
            'body' => 'required|string'
        ]);

        $user = $this->getUser();

        $note = $user->notes()->create($validatedData);

        return ApiResponse::success(
            NoteResource::make($note)
        );
    }

    #[
        Endpoint('Обновление названия', 'Обновить название заметки'),
        BodyParam('title', 'string', 'Название заметки, макс 255 символов'),
        ResponseFromApiResource(NoteResource::class, Note::class)
    ]
    public function updateTitle(Request $request, Note $note): JsonResponse
    {
        $this->authorize('update', $note);

        $validatedData = $this->validate($request, [
            'title' => 'required|string|max:255',
        ]);

        $note->update($validatedData);

        return ApiResponse::success(
            NoteResource::make($note)
        );
    }

    #[
        Endpoint('Обновление содержимого', 'Обновить содержимое заметки'),
        BodyParam('body', 'string', 'Содержимое заметки'),
        ResponseFromApiResource(NoteResource::class, Note::class)
    ]
    public function updateBody(Request $request, Note $note): JsonResponse
    {
        $this->authorize('update', $note);

        $validatedData = $this->validate($request, [
            'body' => 'required|string',
        ]);

        $note->update($validatedData);

        return ApiResponse::success(
            NoteResource::make($note)
        );
    }

    #[Endpoint('Удаление заметки', 'Удалить заметку')]
    public function destroy(Note $note): JsonResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        return ApiResponse::success([], code: Response::HTTP_NO_CONTENT);
    }
}
