<?php

namespace App\Actions\Task;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskStatisticsAction
{
  public function handle()
  {
    $user = Auth::user();
    $query = Task::query();

    // Only show user's own tasks (unless admin)
    if ($user->user_type !== 'admin') {
      $query->where(function ($q) use ($user) {
        $q->where('created_by', $user->id)
          ->orWhere('assigned_to', $user->id);
      });
    }

    // Basic statistics
    $totalTasks = clone ($query);
    $totalTasks = $totalTasks->count();

    $completedTasks = clone ($query);
    $completedTasks = $completedTasks->where('status', 'completed')->count();

    $pendingTasks = clone ($query);
    $pendingTasks = $pendingTasks->where('status', 'pending')->count();

    // Priority statistics
    $urgentPriorityTasks = clone ($query);
    $urgentPriorityTasks = $urgentPriorityTasks->where('priority', 'urgent')->count();

    $highPriorityTasks = clone ($query);
    $highPriorityTasks = $highPriorityTasks->where('priority', 'high')->count();

    $mediumPriorityTasks = clone ($query);
    $mediumPriorityTasks = $mediumPriorityTasks->where('priority', 'medium')->count();

    $lowPriorityTasks = clone ($query);
    $lowPriorityTasks = $lowPriorityTasks->where('priority', 'low')->count();

    // Tasks by priority for charts
    $tasksByPriority = [
      ['priority' => 'urgent', 'count' => $urgentPriorityTasks, 'label' => 'Urgente'],
      ['priority' => 'high', 'count' => $highPriorityTasks, 'label' => 'Alta'],
      ['priority' => 'medium', 'count' => $mediumPriorityTasks, 'label' => 'Média'],
      ['priority' => 'low', 'count' => $lowPriorityTasks, 'label' => 'Baixa'],
    ];

    // Tasks created in the last 7 days
    $tasksLast7Days = [];
    for ($i = 6; $i >= 0; $i--) {
      $date = Carbon::now()->subDays($i);
      $dayQuery = clone ($query);
      $count = $dayQuery->whereDate('created_at', $date->format('Y-m-d'))->count();
      $tasksLast7Days[] = [
        'date' => $date->format('Y-m-d'),
        'day' => $date->format('D'),
        'count' => $count,
        'label' => $date->format('d/m')
      ];
    }

    // Tasks due this week
    $weekQuery = clone ($query);
    $tasksDueThisWeek = $weekQuery->whereBetween('due_date', [
      Carbon::now()->startOfWeek(),
      Carbon::now()->endOfWeek()
    ])
      ->where('status', '!=', 'completed')
      ->count();

    // Overdue tasks
    $overdueQuery = clone ($query);
    $overdueTasks = $overdueQuery->where('due_date', '<', Carbon::now()->format('Y-m-d'))
      ->where('status', '!=', 'completed')
      ->count();

    return [
      'summary' => [
        'totalTasks' => $totalTasks,
        'completedTasks' => $completedTasks,
        'pendingTasks' => $pendingTasks,
        'tasksDueThisWeek' => $tasksDueThisWeek,
        'overdueTasks' => $overdueTasks,
      ],
      'charts' => [
        'tasksByPriority' => $tasksByPriority,
        'tasksLast7Days' => $tasksLast7Days,
      ]
    ];
  }
}
