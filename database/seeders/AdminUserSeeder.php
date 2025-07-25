<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário administrador padrão
        User::firstOrCreate(
            ['email' => 'admin@todoapp.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@todoapp.com',
                'password' => Hash::make('admin123'),
                'user_type' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Criar alguns usuários comuns para teste
        User::firstOrCreate(
            ['email' => 'user1@todoapp.com'],
            [
                'name' => 'Usuário Teste 1',
                'email' => 'user1@todoapp.com',
                'password' => Hash::make('user123'),
                'user_type' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'user2@todoapp.com'],
            [
                'name' => 'Usuário Teste 2',
                'email' => 'user2@todoapp.com',
                'password' => Hash::make('user123'),
                'user_type' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
