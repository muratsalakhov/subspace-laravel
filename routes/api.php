<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    // авторизация/регистрация
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        // пользователь
        Route::get('/user', [UserController::class, 'show']);

        // виджет список задач
        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']); // получить список задач
            Route::get('/{task}', [TaskController::class, 'show']); // получить задачу

            Route::post('/', [TaskController::class, 'store']); // создать задачу
            Route::put('/{task}', [TaskController::class, 'update']); // изменить задачу
            Route::patch('/{task}', [TaskController::class, 'updateStatus']); // изменить статус задачи
            Route::delete('/{task}', [TaskController::class, 'destroy']); // удалить задачу
        });

        // виджет заметки
        Route::prefix('notes')->group(function () {
            Route::get('/', [NoteController::class, 'index']); // получить список заметок
            Route::get('/{note}', [NoteController::class, 'show']); // получить заметку

            Route::post('/', [NoteController::class, 'store']); // создать заметку
            Route::patch('/{note}/title', [NoteController::class, 'updateTitle']); // изменить название заметки
            Route::patch('/{note}/body', [NoteController::class, 'updateBody']); // изменить содержимое заметки
            Route::delete('/{note}', [NoteController::class, 'destroy']); // удалить заметку
        });
    });
});
