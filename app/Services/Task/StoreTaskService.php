<?php

namespace App\Services\Task;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTaskService
{
  public function handle(Request $request)
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

    // Verificar se o usuário pode atribuir a tarefa ao projeto
    if (isset($validated['project_id'])) {
      $project = Project::find($validated['project_id']);
      if (!$project || $project->created_by !== Auth::id()) {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
          response()->json(['message' => 'Unauthorized to assign task to this project'], 403)
        );
      }
    }

    $assignedTo = $validated['assigned_to'] ?? Auth::id();

    // Only allow admins to assign to others
    if ($request->assigned_to && Auth::user()->user_type !== 'admin') {
      $assignedTo = Auth::id();
    }

    $task = Task::create([
      'title' => $validated['title'],
      'description' => $validated['description'],
      'priority' => $validated['priority'],
      'due_date' => $validated['due_date'],
      'status' => ($validated['completed'] ?? false) ? 'completed' : 'pending',
      'completed_at' => ($validated['completed'] ?? false) ? now() : null,
      'created_by' => Auth::id(),
      'assigned_to' => $assignedTo,
      'project_id' => $validated['project_id'],
    ]);

    $task->load(['creator', 'assignedUser', 'project', 'assignments.user']);

    return $task;
  }
}
