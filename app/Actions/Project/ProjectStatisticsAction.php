<?php

namespace App\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjectStatisticsAction
{
  public function handle(Project $project)
  {
    // Check if user can view this project using policy
    if (!Gate::allows('view', $project)) {
      throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to view this project.');
    }

    $project->load(['tasks.creator', 'tasks.assignedUser']);

    $stats = [
      'total_tasks' => $project->tasks->count(),
      'completed_tasks' => $project->completedTasks()->count(),
      'pending_tasks' => $project->pendingTasks()->count(),
      'progress' => $project->progress,
      'tasks_by_priority' => $project->tasks()
        ->selectRaw('priority, count(*) as count')
        ->groupBy('priority')
        ->pluck('count', 'priority')
        ->toArray(),
      'tasks_by_status' => $project->tasks()
        ->selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray(),
      'overdue_tasks' => $project->tasks()
        ->where('due_date', '<', now()->format('Y-m-d'))
        ->where('status', '!=', 'completed')
        ->count(),
      'upcoming_tasks' => $project->tasks()
        ->where('due_date', '>=', now()->format('Y-m-d'))
        ->where('due_date', '<=', now()->addDays(7)->format('Y-m-d'))
        ->where('status', '!=', 'completed')
        ->count(),
    ];

    return $stats;
  }
}
