<?php

namespace App\Services\Task;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateTaskAction
{
  public function handle(Request $request, Task $task)
  {
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
      $project = Project::find($validated['project_id']);
      if ($project && $project->created_by !== Auth::id()) {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
          response()->json(['message' => 'Unauthorized to assign task to this project'], 403)
        );
      }
    }

    $updateData = $request->only(['title', 'description', 'priority', 'due_date', 'project_id']);

    // Only allow admins to change assignment
    if ($request->has('assigned_to') && Auth::user()->user_type === 'admin') {
      $updateData['assigned_to'] = $request->assigned_to ?: Auth::id();
    }

    if (isset($validated['completed'])) {
      $updateData['status'] = $validated['completed'] ? 'completed' : 'pending';
      if ($validated['completed']) {
        $updateData['completed_at'] = now();
      } else {
        $updateData['completed_at'] = null;
      }
    }

    $task->update($updateData);
    $task->load(['creator', 'assignedUser', 'project', 'assignments.user']);

    return $task;
  }
}
