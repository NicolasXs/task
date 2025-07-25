<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ListUsersAction
{
  public function handle()
  {
    return User::select(['id', 'name', 'email', 'user_type', 'created_at'])
      ->withCount([
        'createdTasks as created_tasks_count',
        'assignedTasks as assigned_tasks_count'
      ])
      ->get()
      ->map(function ($user) {
        // Calculate total tasks without duplication
        // Get unique task IDs from both created and assigned tasks
        $createdTaskIds = $user->createdTasks()->pluck('id')->toArray();
        $assignedTaskIds = $user->assignedTasks()->pluck('id')->toArray();
        $uniqueTaskIds = array_unique(array_merge($createdTaskIds, $assignedTaskIds));
        $user->total_tasks = count($uniqueTaskIds);

        return $user;
      });
  }
}
