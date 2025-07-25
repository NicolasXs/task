<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     title="Project",
 *     description="Modelo de Projeto",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Projeto ABC"),
 *     @OA\Property(property="description", type="string", example="Descrição do projeto"),
 *     @OA\Property(property="color", type="string", example="#FF5733"),
 *     @OA\Property(property="status", type="string", enum={"active", "completed", "on_hold"}, example="active"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2025-07-01"),
 *     @OA\Property(property="end_date", type="string", format="date", nullable=true, example="2025-12-31"),
 *     @OA\Property(property="created_by", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'status',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Usuário que criou o projeto
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Tarefas do projeto
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Tarefas completadas do projeto
     */
    public function completedTasks()
    {
        return $this->hasMany(Task::class)->where('status', 'completed');
    }

    /**
     * Tarefas pendentes do projeto
     */
    public function pendingTasks()
    {
        return $this->hasMany(Task::class)->where('status', '!=', 'completed');
    }

    /**
     * Calcula o progresso do projeto baseado nas tarefas
     */
    public function getProgressAttribute()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->completedTasks()->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Conta o total de tarefas do projeto
     */
    public function getTasksCountAttribute()
    {
        return $this->tasks()->count();
    }
}
