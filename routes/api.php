<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use App\Http\Controllers\Api\V1\NotesApiController;
use App\Http\Controllers\Api\V1\TasksApiController;
use App\Http\Controllers\Api\V1\StudySessionsApiController;

Route::prefix('v1')->group(function () {

    // Public: login -> dapat token
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    // Protected: butuh Bearer token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);

        Route::get('/dashboard/summary', [DashboardApiController::class, 'summary']);

        // Notes
        Route::get('/notes', [NotesApiController::class, 'index']);
        Route::post('/notes', [NotesApiController::class, 'store']);
        Route::patch('/notes/{id}/pin', [NotesApiController::class, 'togglePin']);
        Route::delete('/notes/{id}', [NotesApiController::class, 'destroy']);

        // Tasks
        Route::get('/tasks', [TasksApiController::class, 'index']); // ?from=YYYY-MM-DD&to=YYYY-MM-DD optional
        Route::post('/tasks', [TasksApiController::class, 'store']);
        Route::patch('/tasks/{id}/toggle', [TasksApiController::class, 'toggle']);
        Route::delete('/tasks/{id}', [TasksApiController::class, 'destroy']);

        // Study sessions
        Route::get('/study-sessions', [StudySessionsApiController::class, 'index']); // ?date=YYYY-MM-DD optional
        Route::post('/study-sessions', [StudySessionsApiController::class, 'store']);
    });
});
