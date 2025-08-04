<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\Task\ListTasksService;
use App\Services\Task\StoreTaskService;
use App\Services\Task\ShowTaskService;
use App\Services\Task\UpdateTaskService;
use App\Services\Task\DeleteTaskService;
use App\Services\Task\ToggleTaskService;
use App\Services\Task\ApiTaskStatisticsService;
use App\Services\Task\ExportTasksService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
  use AuthorizesRequests;

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): JsonResponse
  {
    $result = app(ListTasksService::class)->handle($request);
    return response()->json($result);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): JsonResponse
  {
    $task = app(StoreTaskService::class)->handle($request);
    return response()->json($task, 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Task $task): JsonResponse
  {
    $this->authorize('view', $task);

    $task = app(ShowTaskService::class)->handle($task);
    return response()->json($task);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Task $task): JsonResponse
  {
    $this->authorize('update', $task);

    $task = app(UpdateTaskService::class)->handle($request, $task);
    return response()->json($task);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Task $task): JsonResponse
  {
    $this->authorize('delete', $task);

    $result = app(DeleteTaskService::class)->handle($task);
    return response()->json($result);
  }

  /**
   * Toggle task completion status.
   */
  public function toggle(Task $task): JsonResponse
  {
    $this->authorize('update', $task);

    $task = app(ToggleTaskService::class)->handle($task);
    return response()->json($task);
  }

  /**
   * Get task statistics.
   */
  public function stats(): JsonResponse
  {
    $result = app(ApiTaskStatisticsService::class)->handle();
    return response()->json($result);
  }

  /**
   * Export tasks to CSV.
   */
  public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\Response
  {
    return app(ExportTasksService::class)->handle();
  }
}
