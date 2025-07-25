<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /**
     * Tarefa comentada
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Usuário que fez o comentário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
