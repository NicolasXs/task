<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaginateCollection;
use Carbon\Carbon;

class ListTasksService
{
  public function handle(Request $request)
  {
    $user = Auth::user();
    $query = Task::query();

    if ($user->user_type !== 'admin') {
      $query->where(function ($q) use ($user) {
        $q->where('created_by', $user->id)
          ->orWhere('assigned_to', $user->id);
      });
    }

    if ($request->filled('search') && trim($request->search) !== '') {
      $query->search($request->search);
    }

    if ($request->filled('status')) {
      $query->status($request->status);
    }

    if ($request->filled('priority')) {
      $query->priority($request->priority);
    }

    if ($request->filled('project_id') && $request->project_id !== 'all') {
      if ($request->project_id === 'null' || $request->project_id === '') {
        $query->whereNull('project_id');
      } else {
        $query->where('project_id', $request->project_id);
      }
    }

    if ($request->filled('filter') && $request->filter !== 'all') {
      switch ($request->filter) {
        case 'completed':
          $query->where('status', 'completed');
          break;
        case 'pending':
          $query->whereNotIn('status', ['completed']);
          break;
        case 'overdue':
          $query->where('due_date', '<', Carbon::now()->format('Y-m-d'))
            ->where('status', '!=', 'completed');
          break;
        case 'high':
        case 'medium':
        case 'low':
          $query->where('priority', $request->filter);
          break;
      }
    }

    switch ($request->get('sort', 'newest')) {
      case 'oldest':
        $query->orderBy('created_at', 'asc');
        break;
      case 'priority':
        $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
        break;
      case 'dueDate':
        $query->orderBy('due_date', 'asc');
        break;
      default: 
        $query->orderBy('created_at', 'desc');
    }

    $tasks = $query->with(['creator', 'assignedUser', 'project'])->paginate(
      $request->get('per_page', 10)
    );

    return new PaginateCollection($tasks);
  }
}
