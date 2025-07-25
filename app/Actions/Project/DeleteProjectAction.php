<?php

namespace App\Actions\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DeleteProjectAction
{
  public function handle(Project $project)
  {
    // Check if user can delete this project using policy
    if (!Gate::allows('delete', $project)) {
      throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to delete this project.');
    }

    // Move all tasks in this project to "No Project" (set project_id to null)
    $project->tasks()->update(['project_id' => null]);

    // Delete the project
    $project->delete();

    return true;
  }
}
