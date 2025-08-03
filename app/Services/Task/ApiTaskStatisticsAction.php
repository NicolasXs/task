<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class ApiTaskStatisticsAction
{
  public function handle()
  {
    $userId = Auth::id();

    // Usar as mesmas queries que estavam no controller da API
    $stats = [
      'total' => Task::where(function ($q) use ($userId) {
        $q->where('created_by', $userId)
          ->orWhere('assigned_to', $userId);
      })->count(),

      'completed' => Task::where(function ($q) use ($userId) {
        $q->where('created_by', $userId)
          ->orWhere('assigned_to', $userId);
      })->where('status', 'completed')->count(),

      'pending' => Task::where(function ($q) use ($userId) {
        $q->where('created_by', $userId)
          ->orWhere('assigned_to', $userId);
      })->where('status', '!=', 'completed')->count(),

      'highPriority' => Task::where(function ($q) use ($userId) {
        $q->where('created_by', $userId)
          ->orWhere('assigned_to', $userId);
      })->where('priority', 'high')->count(),

      'mediumPriority' => Task::where(function ($q) use ($userId) {
        $q->where('created_by', $userId)
          ->orWhere('assigned_to', $userId);
      })->where('priority', 'medium')->count(),

      'lowPriority' => Task::where(function ($q) use ($userId) {
        $q->where('created_by', $userId)
          ->orWhere('assigned_to', $userId);
      })->where('priority', 'low')->count(),
    ];

    return $stats;
  }
}
