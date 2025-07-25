<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
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
    $query = Task::with(['creator', 'assignedUser', 'project', 'assignments.user'])
      ->where(function ($q) {
        $q->where('created_by', Auth::id())
          ->orWhere('assigned_to', Auth::id());
      });

    // Filter by project
    if ($request->has('project_id') && $request->project_id !== 'all') {
      if ($request->project_id === 'null' || $request->project_id === '') {
        $query->whereNull('project_id');
      } else {
        $query->where('project_id', $request->project_id);
      }
    }

    // Search
    if ($request->has('search') && $request->search) {
      $query->where(function ($q) use ($request) {
        $q->where('title', 'like', '%' . $request->search . '%')
          ->orWhere('description', 'like', '%' . $request->search . '%');
      });
    }

    // Filter
    if ($request->has('filter') && $request->filter !== 'all') {
      switch ($request->filter) {
        case 'completed':
          $query->where('status', 'completed');
          break;
        case 'pending':
          $query->where('status', '!=', 'completed');
          break;
        case 'high':
        case 'medium':
        case 'low':
          $query->where('priority', $request->filter);
          break;
      }
    }

    // Sort
    switch ($request->get('sort', 'newest')) {
      case 'oldest':
        $query->orderBy('created_at', 'asc');
        break;
      case 'priority':
        $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
        break;
      case 'dueDate':
        $query->orderBy('due_date', 'asc');
        break;
      default: // newest
        $query->orderBy('created_at', 'desc');
        break;
    }

    // Pagination
    $perPage = $request->get('per_page', 5);
    $tasks = $query->paginate($perPage);

    return response()->json($tasks);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'priority' => ['required', Rule::in(['high', 'medium', 'low', 'urgent'])],
      'due_date' => 'nullable|date|after_or_equal:today',
      'completed' => 'boolean',
      'project_id' => 'nullable|exists:projects,id',
      'assigned_to' => 'nullable|exists:users,id'
    ]);

    $validated['created_by'] = Auth::id();
    $validated['status'] = $validated['completed'] ?? false ? 'completed' : 'pending';

    if (isset($validated['completed']) && $validated['completed']) {
      $validated['completed_at'] = now();
    }

    unset($validated['completed']);

    // Verificar se o usuário pode atribuir a tarefa ao projeto
    if (isset($validated['project_id'])) {
      $project = \App\Models\Project::find($validated['project_id']);
      if (!$project || $project->created_by !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized to assign task to this project'], 403);
      }
    }

    $task = Task::create($validated);
    $task->load(['creator', 'assignedUser', 'project', 'assignments.user']);

    return response()->json($task, 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Task $task): JsonResponse
  {
    $this->authorize('view', $task);

    $task->load(['user', 'assignments.user', 'comments.user']);
    return response()->json($task);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Task $task): JsonResponse
  {
    $this->authorize('update', $task);

    $validated = $request->validate([
      'title' => 'sometimes|required|string|max:255',
      'description' => 'nullable|string',
      'priority' => ['sometimes', Rule::in(['high', 'medium', 'low', 'urgent'])],
      'due_date' => 'nullable|date|after_or_equal:today',
      'completed' => 'boolean',
      'project_id' => 'nullable|exists:projects,id',
      'assigned_to' => 'nullable|exists:users,id'
    ]);

    // Verificar se o usuário pode atribuir a tarefa ao projeto
    if (isset($validated['project_id'])) {
      $project = \App\Models\Project::find($validated['project_id']);
      if ($project && $project->created_by !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized to assign task to this project'], 403);
      }
    }

    if (isset($validated['completed'])) {
      $validated['status'] = $validated['completed'] ? 'completed' : 'pending';
      if ($validated['completed']) {
        $validated['completed_at'] = now();
      } else {
        $validated['completed_at'] = null;
      }
      unset($validated['completed']);
    }

    $task->update($validated);
    $task->load(['creator', 'assignedUser', 'project', 'assignments.user']);

    return response()->json($task);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Task $task): JsonResponse
  {
    $this->authorize('delete', $task);

    $task->delete();

    return response()->json(['message' => 'Task deleted successfully']);
  }

  /**
   * Toggle task completion status.
   */
  public function toggle(Task $task): JsonResponse
  {
    $this->authorize('update', $task);

    $task->status = $task->status === 'completed' ? 'pending' : 'completed';
    $task->save();

    $task->load(['user', 'assignments.user']);

    return response()->json($task);
  }

  /**
   * Get task statistics.
   */
  public function stats(): JsonResponse
  {
    $userId = Auth::id();

    $stats = [
      'total' => Task::where('user_id', $userId)->count(),
      'completed' => Task::where('user_id', $userId)->where('status', 'completed')->count(),
      'pending' => Task::where('user_id', $userId)->where('status', '!=', 'completed')->count(),
      'highPriority' => Task::where('user_id', $userId)->where('priority', 'high')->count(),
      'mediumPriority' => Task::where('user_id', $userId)->where('priority', 'medium')->count(),
      'lowPriority' => Task::where('user_id', $userId)->where('priority', 'low')->count(),
    ];

    return response()->json($stats);
  }

  /**
   * Export tasks to CSV.
   */
  public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\Response
  {
    $query = Task::where('user_id', Auth::id());

    // Apply same filters as index
    if ($request->has('search') && $request->search) {
      $query->where(function ($q) use ($request) {
        $q->where('title', 'like', '%' . $request->search . '%')
          ->orWhere('description', 'like', '%' . $request->search . '%');
      });
    }

    if ($request->has('filter') && $request->filter !== 'all') {
      switch ($request->filter) {
        case 'completed':
          $query->where('status', 'completed');
          break;
        case 'pending':
          $query->where('status', '!=', 'completed');
          break;
        case 'high':
        case 'medium':
        case 'low':
          $query->where('priority', $request->filter);
          break;
      }
    }

    $tasks = $query->get();

    $csvData = "ID,Title,Description,Priority,Due Date,Status\n";

    foreach ($tasks as $task) {
      $csvData .= sprintf(
        '"%s","%s","%s","%s","%s","%s"' . "\n",
        $task->id,
        str_replace('"', '""', $task->title),
        str_replace('"', '""', $task->description ?? ''),
        $task->priority,
        $task->due_date ?? '',
        $task->status
      );
    }

    return response($csvData)
      ->header('Content-Type', 'text/csv')
      ->header('Content-Disposition', 'attachment; filename="tasks_export_' . date('Y-m-d') . '.csv"');
  }
}
