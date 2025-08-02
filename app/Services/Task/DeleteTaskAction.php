<?php

namespace App\Services\Task;

use App\Models\Task;

class DeleteTaskAction
{
  public function handle(Task $task)
  {
    $task->delete();
    return ['message' => 'Task deleted successfully'];
  }
}
