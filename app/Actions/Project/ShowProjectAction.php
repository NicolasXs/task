<?php

namespace App\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ShowProjectAction
{
  public function handle(Project $project)
  {
    // Check if user can view this project using policy
    if (!Gate::allows('view', $project)) {
      throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to view this project.');
    }

    $project->load(['creator', 'tasks.creator', 'tasks.assignedUser']);

    // Add calculated fields
    $project->progress = $project->progress;
    $project->total_tasks = $project->tasks->count();
    $project->completed_tasks = $project->completedTasks()->count();
    $project->pending_tasks = $project->pendingTasks()->count();

    return $project;
  }
}
