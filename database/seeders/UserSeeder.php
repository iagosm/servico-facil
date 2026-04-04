<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $senha = Hash::make('123');

        // Supervisor
        $carlos = User::updateOrCreate(
            ['email' => 'carlos@servicofacil.com'],
            [
                'name'        => 'Carlos Eduardo',
                'password'    => $senha,
                'cargo'       => 'supervisor',
                'telefone'    => '(11) 98765-4321',
                'ativo'       => true,
                'supervisor_id' => null,
            ]
        );

        // Técnicos
        User::updateOrCreate(
            ['email' => 'ana@servicofacil.com'],
            [
                'name'          => 'Ana Paula',
                'password'      => $senha,
                'cargo'         => 'tecnico',
                'telefone'      => '(11) 91234-5678',
                'ativo'         => true,
                'supervisor_id' => $carlos->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'joao@servicofacil.com'],
            [
                'name'          => 'João Mendes',
                'password'      => $senha,
                'cargo'         => 'tecnico',
                'telefone'      => '(11) 93456-7890',
                'ativo'         => true,
                'supervisor_id' => $carlos->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'fernanda@servicofacil.com'],
            [
                'name'          => 'Fernanda Lima',
                'password'      => $senha,
                'cargo'         => 'tecnico',
                'telefone'      => '(11) 94567-8901',
                'ativo'         => true,
                'supervisor_id' => $carlos->id,
            ]
        );

        // Atendente
        User::updateOrCreate(
            ['email' => 'marina@servicofacil.com'],
            [
                'name'          => 'Marina Souza',
                'password'      => $senha,
                'cargo'         => 'atendente',
                'telefone'      => '(11) 95678-9012',
                'ativo'         => true,
                'supervisor_id' => $carlos->id,
            ]
        );

        // Técnico inativo (para testar)
        User::updateOrCreate(
            ['email' => 'roberto@servicofacil.com'],
            [
                'name'          => 'Roberto Alves',
                'password'      => $senha,
                'cargo'         => 'tecnico',
                'telefone'      => '(11) 96789-0123',
                'ativo'         => false,
                'supervisor_id' => $carlos->id,
            ]
        );
    }
}