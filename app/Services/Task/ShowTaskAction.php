<?php

namespace App\Services\Task;

use App\Models\Task;

class ShowTaskAction
{
  public function handle(Task $task)
  {
    $task->load(['creator', 'assignedUser', 'comments.user']);
    return $task;
  }
}
