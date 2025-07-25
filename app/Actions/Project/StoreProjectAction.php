<?php

namespace App\Actions\Project;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreProjectAction
{
  public function handle(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'color' => 'nullable|string|max:7', // For hex color codes
      'status' => 'in:active,on_hold,completed,cancelled,archived',
      'start_date' => 'nullable|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    $project = Project::create([
      'name' => $request->name,
      'description' => $request->description,
      'color' => $request->color ?? '#3B82F6', // Default blue color
      'status' => $request->status ?? 'active',
      'start_date' => $request->start_date,
      'end_date' => $request->end_date,
      'created_by' => Auth::id(),
    ]);

    $project->load(['creator', 'tasks']);

    return $project;
  }
}
