<?php

namespace App\Actions\Task;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreTaskAction
{
  public function handle(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'priority' => 'in:low,medium,high',
      'due_date' => 'nullable|date',
      'completed' => 'boolean',
      'assigned_to' => 'nullable|exists:users,id',
      'project_id' => 'nullable|exists:projects,id'
    ]);

    $assignedTo = Auth::id(); // Default to current user

    // Only allow admins to assign to other users
    if ($request->assigned_to && Auth::user()->user_type === 'admin') {
      $assignedTo = $request->assigned_to;
    }

    $task = Task::create([
      'title' => $request->title,
      'description' => $request->description,
      'priority' => $request->priority ?? 'medium',
      'due_date' => $request->due_date,
      'status' => $request->completed ? 'completed' : 'pending',
      'completed_at' => $request->completed ? now() : null,
      'created_by' => Auth::id(),
      'assigned_to' => $assignedTo,
      'project_id' => $request->project_id,
    ]);

    $task->load(['creator', 'assignedUser', 'project']);

    return $task;
  }
}
