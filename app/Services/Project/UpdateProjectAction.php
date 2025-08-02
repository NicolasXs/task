<?php

namespace App\Services\Project;

use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UpdateProjectAction
{
  public function handle(Project $project, Request $request)
  {
    if (!Gate::allows('update', $project)) {
      throw new AuthorizationException('You are not authorized to update this project.');
    }

    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'color' => 'nullable|string|max:7', // For hex color codes
      'status' => 'in:active,on_hold,completed,cancelled,archived',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    $project->update([
      'name' => $request->name,
      'description' => $request->description,
      'color' => $request->color ?? $project->color,
      'status' => $request->status ?? $project->status,
      'start_date' => $request->start_date,
      'end_date' => $request->end_date,
    ]);

    $project->load(['creator', 'tasks']);

    return $project;
  }
}
