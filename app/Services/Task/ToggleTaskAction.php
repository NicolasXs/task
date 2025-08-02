<?php

namespace App\Services\Task;

use App\Models\Task;

class ToggleTaskAction
{
  public function handle(Task $task)
  {
    $completed = $task->status !== 'completed';

    $task->update([
      'status' => $completed ? 'completed' : 'pending',
      'completed_at' => $completed ? now() : null,
    ]);

    $task->load(['creator', 'assignedUser']);

    return $task;
  }
}
