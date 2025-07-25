<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Profile/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Test route to check if tasks exist
Route::get('/test-tasks', function () {
    return response()->json([
        'users_count' => \App\Models\User::count(),
        'tasks_count' => \App\Models\Task::count(),
        'sample_task' => \App\Models\Task::first()
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Routes for Dashboard (using web middleware for session auth)
    Route::prefix('api')->name('api.')->group(function () {
        // Task routes
        Route::apiResource('tasks', TaskController::class);
        Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
        Route::get('tasks/export/csv', [TaskController::class, 'export'])->name('tasks.export');
        Route::get('tasks/statistics', [TaskController::class, 'statistics'])->name('tasks.statistics');

        // User routes (admin only)
        Route::apiResource('users', UserController::class);
    });
});

require __DIR__ . '/auth.php';
