<?php

namespace App\Services\Project;

use App\Models\Project;
use App\Http\Resources\PaginateCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListProjectsAction
{
  public function handle(Request $request)
  {
    $user = Auth::user();
    $query = Project::query();

    // Only show user's own projects (unless admin)
    if ($user->user_type !== 'admin') {
      $query->where('created_by', $user->id);
    }

    // Search by name or description
    if ($request->filled('search') && trim($request->search) !== '') {
      $searchTerm = trim($request->search);
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'like', "%{$searchTerm}%")
          ->orWhere('description', 'like', "%{$searchTerm}%");
      });
    }

    // Filter by status
    if ($request->filled('status') && $request->status !== 'all') {
      $query->where('status', $request->status);
    }

    // Sort
    $sortBy = $request->get('sort', 'newest');

    // Aplicar ordenação baseada no parâmetro
    switch ($sortBy) {
      case 'newest':
        $query->orderBy('created_at', 'desc');
        break;
      case 'oldest':
        $query->orderBy('created_at', 'asc');
        break;
      case 'name':
        $query->orderBy('name', 'asc');
        break;
      case 'status':
        $query->orderBy('status', 'asc');
        break;
      case 'end_date':
        $query->orderBy('end_date', 'asc');
        break;
      case 'progress':
        $query->withCount([
          'tasks as total_tasks_count',
          'tasks as completed_tasks_count' => function ($q) {
            $q->where('status', 'completed');
          }
        ])
          ->orderByRaw('(completed_tasks_count / NULLIF(total_tasks_count, 0)) DESC');
        break;
      default:
        $query->orderBy('created_at', 'desc');
    }

    // Load relationships and paginate
    $perPage = $request->get('per_page', 10);
    $projects = $query->with(['creator', 'tasks'])
      ->paginate($perPage);

    // Add progress calculation to each project
    $projects->getCollection()->transform(function ($project) {
      $project->progress = $project->progress;
      $project->tasks_count = $project->tasks_count;
      $project->total_tasks = $project->tasks->count();
      $project->completed_tasks = $project->completedTasks()->count();
      $project->pending_tasks = $project->pendingTasks()->count();
      return $project;
    });

    return new PaginateCollection($projects);
  }
}
