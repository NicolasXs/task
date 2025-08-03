<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\Task\ListTasksAction;
use App\Services\Task\StoreTaskAction;
use App\Services\Task\ShowTaskAction;
use App\Services\Task\UpdateTaskAction;
use App\Services\Task\DeleteTaskAction;
use App\Services\Task\ToggleTaskAction;
use App\Services\Task\ApiTaskStatisticsAction;
use App\Services\Task\ExportTasksAction;
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
    $result = app(ListTasksAction::class)->handle($request);
    return response()->json($result);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): JsonResponse
  {
    $task = app(StoreTaskAction::class)->handle($request);
    return response()->json($task, 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Task $task): JsonResponse
  {
    $this->authorize('view', $task);

    $task = app(ShowTaskAction::class)->handle($task);
    return response()->json($task);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Task $task): JsonResponse
  {
    $this->authorize('update', $task);

    $task = app(UpdateTaskAction::class)->handle($request, $task);
    return response()->json($task);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Task $task): JsonResponse
  {
    $this->authorize('delete', $task);

    $result = app(DeleteTaskAction::class)->handle($task);
    return response()->json($result);
  }

  /**
   * Toggle task completion status.
   */
  public function toggle(Task $task): JsonResponse
  {
    $this->authorize('update', $task);

    $task = app(ToggleTaskAction::class)->handle($task);
    return response()->json($task);
  }

  /**
   * Get task statistics.
   */
  public function stats(): JsonResponse
  {
    $result = app(ApiTaskStatisticsAction::class)->handle();
    return response()->json($result);
  }

  /**
   * Export tasks to CSV.
   */
  public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\Response
  {
    return app(ExportTasksAction::class)->handle();
  }
}
