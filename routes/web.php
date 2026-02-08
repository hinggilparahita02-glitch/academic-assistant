<?php
use App\Http\Middleware\SimpleAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TimerController;

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(\App\Http\Middleware\SimpleAuth::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/notes', [NotesController::class, 'index'])->name('notes');
    Route::post('/notes', [NotesController::class, 'store'])->name('notes.store');
    Route::post('/notes/{id}/pin', [NotesController::class, 'togglePin'])->name('notes.pin');
    Route::post('/notes/{id}/delete', [NotesController::class, 'destroy'])->name('notes.delete');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::post('/tasks', [CalendarController::class, 'store'])->name('tasks.store');
    Route::post('/tasks/{id}/toggle', [CalendarController::class, 'toggle'])->name('tasks.toggle');
    Route::post('/tasks/{id}/delete', [CalendarController::class, 'destroy'])->name('tasks.delete');

    Route::get('/timer', [TimerController::class, 'index'])->name('timer');
    Route::post('/timer/log', [TimerController::class, 'log'])->name('timer.log');
});
