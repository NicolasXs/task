<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\AuthController;

// Public authentication routes
Route::prefix('auth')->group(function () {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('register', [AuthController::class, 'register']);

  // Protected auth routes
  Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
  });
});

/**
 * @OA\Get(
 *     path="/user",
 *     summary="Obter usuário autenticado",
 *     description="Retorna os dados do usuário atualmente autenticado",
 *     operationId="getCurrentUser",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Usuário autenticado recuperado com sucesso",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Não autorizado",
 *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
 *     )
 * )
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware(['auth:sanctum', 'web'])->group(function () {
  // Task routes - specific routes must come before resource routes
  Route::get('tasks/statistics', [TaskController::class, 'statistics']);
  Route::get('tasks/export/csv', [TaskController::class, 'export']);
  Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle']);
  Route::apiResource('tasks', TaskController::class);

  // Project routes
  Route::get('projects/{project}/statistics', [ProjectController::class, 'statistics']);
  Route::apiResource('projects', ProjectController::class);

  // User routes (admin only)
  Route::apiResource('users', UserController::class);
});
