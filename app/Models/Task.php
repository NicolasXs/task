<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     title="Task",
 *     description="Modelo de Tarefa",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Minha tarefa"),
 *     @OA\Property(property="description", type="string", example="Descrição da tarefa"),
 *     @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="pending"),
 *     @OA\Property(property="priority", type="string", enum={"low", "medium", "high"}, example="medium"),
 *     @OA\Property(property="due_date", type="string", format="date", example="2025-07-30"),
 *     @OA\Property(property="completed_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(property="assigned_to", type="integer", nullable=true, example=2),
 *     @OA\Property(property="project_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'created_by',
        'assigned_to',
        'project_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Usuário que criou a tarefa
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuário ao qual a tarefa foi atribuída
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Atribuições da tarefa
     */
    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    /**
     * Projeto ao qual a tarefa pertence
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Comentários da tarefa
     */
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Marcar tarefa como concluída
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Verificar se a tarefa está atrasada
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Accessor for completed attribute (for API compatibility)
     */
    public function getCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mutator for completed attribute (for API compatibility)
     */
    public function setCompletedAttribute($value): void
    {
        $this->attributes['status'] = $value ? 'completed' : 'pending';
        if ($value) {
            $this->attributes['completed_at'] = now();
        } else {
            $this->attributes['completed_at'] = null;
        }
    }

    /**
     * Scope for searching tasks
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
