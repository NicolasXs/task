<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateTaskAction
{
  public function handle(Request $request, Task $task)
  {
    $request->validate([
      'title' => 'string|max:255',
      'description' => 'nullable|string',
      'priority' => 'in:low,medium,high',
      'due_date' => 'nullable|date',
      'completed' => 'boolean',
      'assigned_to' => 'nullable|exists:users,id',
      'project_id' => 'nullable|exists:projects,id'
    ]);

    $updateData = $request->only(['title', 'description', 'priority', 'due_date', 'project_id']);

    // Only allow admins to change assignment
    if ($request->has('assigned_to') && Auth::user()->user_type === 'admin') {
      $updateData['assigned_to'] = $request->assigned_to ?: Auth::id();
    }

    if ($request->has('completed')) {
      $updateData['status'] = $request->completed ? 'completed' : 'pending';
      $updateData['completed_at'] = $request->completed ? now() : null;
    }

    $task->update($updateData);
    $task->load(['creator', 'assignedUser', 'project']);

    return $task;
  }
}
