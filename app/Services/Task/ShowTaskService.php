<?php

namespace App\Services\Task;

use App\Models\Task;

class ShowTaskService
{
  public function handle(Task $task)
  {
    $task->load(['creator', 'assignedUser', 'project', 'assignments.user', 'comments.user']);
    return $task;
  }
}
