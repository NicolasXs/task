<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('user_type', 'admin')->first();

        if ($admin) {
            $projects = [
                [
                    'name' => 'Sistema de Gestão de Tarefas',
                    'description' => 'Desenvolvimento completo do sistema de to-do com funcionalidades avançadas',
                    'color' => '#3B82F6',
                    'status' => 'active',
                    'start_date' => now()->subDays(30),
                    'end_date' => now()->addDays(60),
                    'created_by' => $admin->id,
                ],
                [
                    'name' => 'Aplicativo Mobile',
                    'description' => 'Criação do aplicativo móvel para Android e iOS',
                    'color' => '#10B981',
                    'status' => 'active',
                    'start_date' => now()->subDays(15),
                    'end_date' => now()->addDays(90),
                    'created_by' => $admin->id,
                ],
                [
                    'name' => 'API de Integração',
                    'description' => 'API RESTful para integração com sistemas externos',
                    'color' => '#F59E0B',
                    'status' => 'completed',
                    'start_date' => now()->subDays(60),
                    'end_date' => now()->subDays(10),
                    'created_by' => $admin->id,
                ],
                [
                    'name' => 'Documentação Técnica',
                    'description' => 'Criação da documentação completa do sistema',
                    'color' => '#8B5CF6',
                    'status' => 'archived',
                    'start_date' => now()->subDays(45),
                    'end_date' => now()->subDays(20),
                    'created_by' => $admin->id,
                ],
            ];

            foreach ($projects as $projectData) {
                Project::create($projectData);
            }
        }
    }
}
