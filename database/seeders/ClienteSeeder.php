<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nome'      => 'João Silva Santos',
                'telefone'  => '(11) 99999-1234',
                'email'     => 'joao.silva@email.com',
                'endereco'  => 'Rua das Flores, 123',
                'cidade'    => 'São Paulo',
                'estado'    => 'SP',
                'cep'       => '01310-100',
                'documento' => '123.456.789-00',
                'observacoes' => null,
            ],
            [
                'nome'      => 'Maria Aparecida Oliveira',
                'telefone'  => '(21) 98888-5678',
                'email'     => 'maria.oliveira@email.com',
                'endereco'  => 'Av. Copacabana, 456',
                'cidade'    => 'Rio de Janeiro',
                'estado'    => 'RJ',
                'cep'       => '22070-011',
                'documento' => '987.654.321-00',
                'observacoes' => 'Cliente frequente, sempre traz iPhone.',
            ],
            [
                'nome'      => 'Pedro Henrique Costa',
                'telefone'  => '(31) 97777-9012',
                'email'     => 'pedro.costa@email.com',
                'endereco'  => 'Rua da Bahia, 789',
                'cidade'    => 'Belo Horizonte',
                'estado'    => 'MG',
                'cep'       => '30160-011',
                'documento' => '456.789.123-00',
                'observacoes' => null,
            ],
            [
                'nome'      => 'Ana Claudia Ferreira',
                'telefone'  => '(41) 96666-3456',
                'email'     => 'ana.ferreira@email.com',
                'endereco'  => 'Rua XV de Novembro, 321',
                'cidade'    => 'Curitiba',
                'estado'    => 'PR',
                'cep'       => '80020-310',
                'documento' => '321.654.987-00',
                'observacoes' => 'Prefere ser contatada por WhatsApp.',
            ],
            [
                'nome'      => 'Carlos Roberto Souza',
                'telefone'  => '(51) 95555-7890',
                'email'     => 'carlos.souza@email.com',
                'endereco'  => 'Av. Ipiranga, 654',
                'cidade'    => 'Porto Alegre',
                'estado'    => 'RS',
                'cep'       => '90160-093',
                'documento' => '789.123.456-00',
                'observacoes' => null,
            ],
            [
                'nome'      => 'Fernanda Rocha Almeida',
                'telefone'  => '(61) 94444-1234',
                'email'     => 'fernanda.rocha@email.com',
                'endereco'  => 'SQN 312, Bloco A',
                'cidade'    => 'Brasília',
                'estado'    => 'DF',
                'cep'       => '70766-010',
                'documento' => '654.321.789-00',
                'observacoes' => null,
            ],
            [
                'nome'      => 'Ricardo Mendes Barbosa',
                'telefone'  => '(11) 93333-4567',
                'email'     => 'ricardo.mendes@email.com',
                'endereco'  => 'Rua Augusta, 987',
                'cidade'    => 'São Paulo',
                'estado'    => 'SP',
                'cep'       => '01304-000',
                'documento' => '111.222.333-00',
                'observacoes' => 'Empresário, traz equipamentos da empresa.',
            ],
            [
                'nome'      => 'Juliana Martins Pereira',
                'telefone'  => '(11) 92222-5678',
                'email'     => 'juliana.martins@email.com',
                'endereco'  => 'Rua Oscar Freire, 147',
                'cidade'    => 'São Paulo',
                'estado'    => 'SP',
                'cep'       => '01426-001',
                'documento' => '444.555.666-00',
                'observacoes' => null,
            ],
            [
                'nome'      => 'Marcos Paulo Rodrigues',
                'telefone'  => '(19) 91111-6789',
                'email'     => 'marcos.rodrigues@email.com',
                'endereco'  => 'Av. Brasil, 258',
                'cidade'    => 'Campinas',
                'estado'    => 'SP',
                'cep'       => '13013-001',
                'documento' => '777.888.999-00',
                'observacoes' => null,
            ],
            [
                'nome'      => 'Luciana Carvalho Lima',
                'telefone'  => '(11) 90000-7890',
                'email'     => 'luciana.lima@email.com',
                'endereco'  => 'Rua Consolação, 369',
                'cidade'    => 'São Paulo',
                'estado'    => 'SP',
                'cep'       => '01302-000',
                'documento' => '000.111.222-33',
                'observacoes' => 'Leva sempre notebook para manutenção.',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::updateOrCreate(
                ['email' => $cliente['email']],
                $cliente
            );
        }
    }
}