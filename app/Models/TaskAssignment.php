<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'assignment_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Tarefa atribuída
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Usuário ao qual a tarefa foi atribuída
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Usuário que fez a atribuição
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
