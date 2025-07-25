<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the application with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up the application...');

        // Check if admin user exists
        $admin = User::where('email', 'admin@todo.com')->first();

        if (!$admin) {
            $this->info('Creating admin user...');
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@todo.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'is_active' => true,
            ]);
            $this->info('Admin user created with email: admin@todo.com and password: password');
        }

        // Check if regular user exists
        $user = User::where('email', 'user@todo.com')->first();

        if (!$user) {
            $this->info('Creating regular user...');
            $user = User::create([
                'name' => 'Regular User',
                'email' => 'user@todo.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'user_type' => 'user',
                'is_active' => true,
            ]);
            $this->info('Regular user created with email: user@todo.com and password: password');
        }

        // Check if tasks exist
        $taskCount = Task::count();

        if ($taskCount === 0) {
            $this->info('Creating sample tasks...');

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
                    'assigned_to' => $user->id,
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
                    'assigned_to' => $user->id,
                ],
                [
                    'title' => 'Documentar API',
                    'description' => 'Criar documentação completa da API RESTful',
                    'status' => 'pending',
                    'priority' => 'medium',
                    'due_date' => now()->addDays(10),
                    'created_by' => $user->id,
                    'assigned_to' => $user->id,
                ],
            ];

            foreach ($tasks as $taskData) {
                Task::create($taskData);
            }

            $this->info('Sample tasks created successfully!');
        }

        $this->info('Application setup completed!');
        $this->info('You can now login with:');
        $this->info('Admin: admin@todo.com / password');
        $this->info('User: user@todo.com / password');
    }
}
