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

        // Supervisores
        $carlos = User::updateOrCreate(
            ['email' => 'carlos@servicofacil.com'],
            [
                'name'          => 'Carlos Eduardo',
                'password'      => $senha,
                'cargo'         => 'supervisor',
                'telefone'      => '(11) 98765-4321',
                'ativo'         => true,
                'supervisor_id' => null,
            ]
        );

        $marcia = User::updateOrCreate(
            ['email' => 'marcia@servicofacil.com'],
            [
                'name'          => 'Márcia Regina',
                'password'      => $senha,
                'cargo'         => 'supervisor',
                'telefone'      => '(11) 98700-0001',
                'ativo'         => true,
                'supervisor_id' => null,
            ]
        );

        $rodrigo = User::updateOrCreate(
            ['email' => 'rodrigo@servicofacil.com'],
            [
                'name'          => 'Rodrigo Fonseca',
                'password'      => $senha,
                'cargo'         => 'supervisor',
                'telefone'      => '(11) 98700-0002',
                'ativo'         => true,
                'supervisor_id' => null,
            ]
        );

        // Técnicos — time Carlos
        $tecnicos_carlos = [
            ['name' => 'Ana Paula',        'email' => 'ana@servicofacil.com',       'telefone' => '(11) 91234-5678'],
            ['name' => 'João Mendes',      'email' => 'joao@servicofacil.com',      'telefone' => '(11) 93456-7890'],
            ['name' => 'Fernanda Lima',    'email' => 'fernanda@servicofacil.com',  'telefone' => '(11) 94567-8901'],
            ['name' => 'Lucas Oliveira',   'email' => 'lucas@servicofacil.com',     'telefone' => '(11) 91111-0001'],
            ['name' => 'Patrícia Rocha',   'email' => 'patricia@servicofacil.com',  'telefone' => '(11) 91111-0002'],
            ['name' => 'Thiago Almeida',   'email' => 'thiago@servicofacil.com',    'telefone' => '(11) 91111-0003'],
            ['name' => 'Bianca Souza',     'email' => 'bianca@servicofacil.com',    'telefone' => '(11) 91111-0004'],
            ['name' => 'Eduardo Nunes',    'email' => 'eduardo@servicofacil.com',   'telefone' => '(11) 91111-0005'],
            ['name' => 'Camila Freitas',   'email' => 'camila@servicofacil.com',    'telefone' => '(11) 91111-0006'],
            ['name' => 'Felipe Cardoso',   'email' => 'felipe@servicofacil.com',    'telefone' => '(11) 91111-0007'],
            ['name' => 'Gustavo Martins',  'email' => 'gustavo@servicofacil.com',   'telefone' => '(11) 91111-0008'],
            ['name' => 'Helena Costa',     'email' => 'helena@servicofacil.com',    'telefone' => '(11) 91111-0009'],
        ];

        foreach ($tecnicos_carlos as $t) {
            User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name'          => $t['name'],
                    'password'      => $senha,
                    'cargo'         => 'tecnico',
                    'telefone'      => $t['telefone'],
                    'ativo'         => true,
                    'supervisor_id' => $carlos->id,
                ]
            );
        }

        // Técnicos — time Márcia
        $tecnicos_marcia = [
            ['name' => 'Igor Teixeira',    'email' => 'igor@servicofacil.com',      'telefone' => '(11) 92222-0001'],
            ['name' => 'Juliana Pires',    'email' => 'juliana@servicofacil.com',   'telefone' => '(11) 92222-0002'],
            ['name' => 'Klaus Ribeiro',    'email' => 'klaus@servicofacil.com',     'telefone' => '(11) 92222-0003'],
            ['name' => 'Larissa Moura',    'email' => 'larissa@servicofacil.com',   'telefone' => '(11) 92222-0004'],
            ['name' => 'Marcelo Barbosa',  'email' => 'marcelo@servicofacil.com',   'telefone' => '(11) 92222-0005'],
            ['name' => 'Natalia Cunha',    'email' => 'natalia@servicofacil.com',   'telefone' => '(11) 92222-0006'],
            ['name' => 'Otávio Leal',      'email' => 'otavio@servicofacil.com',    'telefone' => '(11) 92222-0007'],
            ['name' => 'Priscila Andrade', 'email' => 'priscila@servicofacil.com',  'telefone' => '(11) 92222-0008'],
            ['name' => 'Rafael Duarte',    'email' => 'rafael@servicofacil.com',    'telefone' => '(11) 92222-0009'],
            ['name' => 'Sabrina Campos',   'email' => 'sabrina@servicofacil.com',   'telefone' => '(11) 92222-0010'],
            ['name' => 'Tiago Ferreira',   'email' => 'tiago@servicofacil.com',     'telefone' => '(11) 92222-0011'],
        ];

        foreach ($tecnicos_marcia as $t) {
            User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name'          => $t['name'],
                    'password'      => $senha,
                    'cargo'         => 'tecnico',
                    'telefone'      => $t['telefone'],
                    'ativo'         => true,
                    'supervisor_id' => $marcia->id,
                ]
            );
        }

        // Técnicos — time Rodrigo
        $tecnicos_rodrigo = [
            ['name' => 'Ursula Batista',   'email' => 'ursula@servicofacil.com',    'telefone' => '(11) 93333-0001'],
            ['name' => 'Vinícius Gomes',   'email' => 'vinicius@servicofacil.com',  'telefone' => '(11) 93333-0002'],
            ['name' => 'Wesley Araújo',    'email' => 'wesley@servicofacil.com',    'telefone' => '(11) 93333-0003'],
            ['name' => 'Ximena Torres',    'email' => 'ximena@servicofacil.com',    'telefone' => '(11) 93333-0004'],
            ['name' => 'Yuri Cavalcante',  'email' => 'yuri@servicofacil.com',      'telefone' => '(11) 93333-0005'],
            ['name' => 'Zélia Nascimento', 'email' => 'zelia@servicofacil.com',     'telefone' => '(11) 93333-0006'],
            ['name' => 'André Vasconcelos','email' => 'andre@servicofacil.com',     'telefone' => '(11) 93333-0007'],
            ['name' => 'Bruna Correia',    'email' => 'bruna@servicofacil.com',     'telefone' => '(11) 93333-0008'],
            ['name' => 'César Mello',      'email' => 'cesar@servicofacil.com',     'telefone' => '(11) 93333-0009'],
            ['name' => 'Diana Pacheco',    'email' => 'diana@servicofacil.com',     'telefone' => '(11) 93333-0010'],
        ];

        foreach ($tecnicos_rodrigo as $t) {
            User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name'          => $t['name'],
                    'password'      => $senha,
                    'cargo'         => 'tecnico',
                    'telefone'      => $t['telefone'],
                    'ativo'         => true,
                    'supervisor_id' => $rodrigo->id,
                ]
            );
        }

        // Atendentes
        $atendentes = [
            ['name' => 'Marina Souza',     'email' => 'marina@servicofacil.com',    'telefone' => '(11) 95678-9012'],
            ['name' => 'Eliane Figueiredo','email' => 'eliane@servicofacil.com',    'telefone' => '(11) 94444-0001'],
            ['name' => 'Fábio Monteiro',   'email' => 'fabio@servicofacil.com',     'telefone' => '(11) 94444-0002'],
            ['name' => 'Gabriela Lopes',   'email' => 'gabriela@servicofacil.com',  'telefone' => '(11) 94444-0003'],
            ['name' => 'Henrique Dias',    'email' => 'henrique@servicofacil.com',  'telefone' => '(11) 94444-0004'],
            ['name' => 'Isabela Paiva',    'email' => 'isabela@servicofacil.com',   'telefone' => '(11) 94444-0005'],
            ['name' => 'Jonas Pereira',    'email' => 'jonas@servicofacil.com',     'telefone' => '(11) 94444-0006'],
            ['name' => 'Karen Medeiros',   'email' => 'karen@servicofacil.com',     'telefone' => '(11) 94444-0007'],
            ['name' => 'Leandro Castro',   'email' => 'leandro@servicofacil.com',   'telefone' => '(11) 94444-0008'],
            ['name' => 'Mônica Silveira',  'email' => 'monica@servicofacil.com',    'telefone' => '(11) 94444-0009'],
        ];

        foreach ($atendentes as $a) {
            User::updateOrCreate(
                ['email' => $a['email']],
                [
                    'name'          => $a['name'],
                    'password'      => $senha,
                    'cargo'         => 'atendente',
                    'telefone'      => $a['telefone'],
                    'ativo'         => true,
                    'supervisor_id' => $carlos->id,
                ]
            );
        }

        // Técnicos inativos (para testes)
        $inativos = [
            ['name' => 'Roberto Alves',    'email' => 'roberto@servicofacil.com',   'telefone' => '(11) 96789-0123'],
            ['name' => 'Sandra Queiroz',   'email' => 'sandra@servicofacil.com',    'telefone' => '(11) 96789-0124'],
            ['name' => 'Paulo Rezende',    'email' => 'paulo@servicofacil.com',     'telefone' => '(11) 96789-0125'],
            ['name' => 'Vanessa Borges',   'email' => 'vanessa@servicofacil.com',   'telefone' => '(11) 96789-0126'],
        ];

        foreach ($inativos as $i) {
            User::updateOrCreate(
                ['email' => $i['email']],
                [
                    'name'          => $i['name'],
                    'password'      => $senha,
                    'cargo'         => 'tecnico',
                    'telefone'      => $i['telefone'],
                    'ativo'         => false,
                    'supervisor_id' => $carlos->id,
                ]
            );
        }
    }
}