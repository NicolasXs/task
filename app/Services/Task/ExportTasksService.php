<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportTasksService
{
  /**
   * Handle the export of tasks to CSV
   *
   * @return StreamedResponse
   */
  public function handle(): StreamedResponse
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

    $tasks = $query->with(['creator', 'assignedUser'])->get();

    $filename = 'tasks_' . date('Y-m-d_H-i-s') . '.csv';

    $headers = [
      'Content-Type' => 'text/csv',
      'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];

    $callback = function () use ($tasks) {
      $file = fopen('php://output', 'w');

      // CSV Header
      fputcsv($file, [
        'ID',
        'Title',
        'Description',
        'Status',
        'Priority',
        'Due Date',
        'Created By',
        'Assigned To',
        'Created At',
        'Completed At'
      ]);

      // CSV Data
      foreach ($tasks as $task) {
        fputcsv($file, [
          $task->id,
          $task->title,
          $task->description,
          $task->status,
          $task->priority,
          $task->due_date ? $task->due_date->format('Y-m-d') : '',
          $task->creator ? $task->creator->name : '',
          $task->assignedUser ? $task->assignedUser->name : '',
          $task->created_at->format('Y-m-d H:i:s'),
          $task->completed_at ? $task->completed_at->format('Y-m-d H:i:s') : '',
        ]);
      }

      fclose($file);
    };

    return response()->stream($callback, 200, $headers);
  }
}
