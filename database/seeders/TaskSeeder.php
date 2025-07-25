<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter usuários existentes
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $admin = $users->where('user_type', 'admin')->first();
        $regularUser = $users->where('user_type', 'user')->first();

        $tasks = [
            [
                'title' => 'Implementar autenticação de usuários',
                'description' => 'Criar sistema de login e registro com Laravel Sanctum',
                'status' => 'completed',
                'priority' => 'high',
                'due_date' => now()->subDays(2),
                'completed_at' => now()->subDays(1),
                'created_by' => $admin->id,
                'assigned_to' => $admin->id,
            ],
            [
                'title' => 'Criar dashboard responsivo',
                'description' => 'Desenvolver interface do dashboard com Vue.js e Tailwind CSS',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => now()->addDays(3),
                'created_by' => $admin->id,
                'assigned_to' => $regularUser->id,
            ],
            [
                'title' => 'Configurar banco de dados',
                'description' => 'Configurar migrations e seeders para o projeto',
                'status' => 'completed',
                'priority' => 'medium',
                'due_date' => now()->subDays(5),
                'completed_at' => now()->subDays(3),
                'created_by' => $admin->id,
                'assigned_to' => $admin->id,
            ],
            [
                'title' => 'Implementar CRUD de tarefas',
                'description' => 'Criar operações de criar, ler, atualizar e deletar tarefas',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => now()->addDays(7),
                'created_by' => $admin->id,
                'assigned_to' => $regularUser->id,
            ],
            [
                'title' => 'Documentar API',
                'description' => 'Criar documentação completa da API RESTful',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addDays(10),
                'created_by' => $regularUser->id,
                'assigned_to' => $regularUser->id,
            ],
            [
                'title' => 'Testes unitários',
                'description' => 'Escrever testes para todas as funcionalidades',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => now()->addDays(14),
                'created_by' => $admin->id,
                'assigned_to' => $admin->id,
            ],
            [
                'title' => 'Otimizar performance',
                'description' => 'Implementar cache e otimizações de consultas',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => now()->addDays(21),
                'created_by' => $regularUser->id,
                'assigned_to' => $regularUser->id,
            ],
            [
                'title' => 'Deploy em produção',
                'description' => 'Configurar servidor e fazer deploy da aplicação',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => now()->addDays(30),
                'created_by' => $admin->id,
                'assigned_to' => $admin->id,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        $this->command->info('Tasks seeded successfully!');
    }
}
