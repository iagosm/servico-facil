<?php

namespace Database\Seeders;

use App\Models\Fornecedor;
use Illuminate\Database\Seeder;

class FornecedorSeeder extends Seeder
{
    public function run(): void
    {
        $fornecedores = [
            [
                'nome'        => 'TechParts São Paulo',
                'telefone'    => '(11) 3333-4444',
                'email'       => 'vendas@techparts.com.br',
                'contato'     => 'Rafael Cunha',
                'site'        => 'techparts.com.br',
                'observacoes' => 'Entrega em até 2 dias úteis para SP. Frete grátis acima de R$ 300.',
                'ativo'       => true,
            ],
            [
                'nome'        => 'PeçasPhone Distribuidora',
                'telefone'    => '(21) 4444-5555',
                'email'       => 'comercial@pecasphone.com.br',
                'contato'     => 'Camila Duarte',
                'site'        => 'pecasphone.com.br',
                'observacoes' => 'Especializada em peças para celulares. Bom prazo e qualidade.',
                'ativo'       => true,
            ],
            [
                'nome'        => 'Distribuidora Sul Eletrônicos',
                'telefone'    => '(51) 5555-6666',
                'email'       => 'pedidos@distsul.com.br',
                'contato'     => 'Thiago Moreira',
                'site'        => 'distsul.com.br',
                'observacoes' => 'Melhor preço em capacitores e componentes eletrônicos.',
                'ativo'       => true,
            ],
            [
                'nome'        => 'GlobalFix Peças e Acessórios',
                'telefone'    => '(11) 6666-7777',
                'email'       => 'atendimento@globalfix.com.br',
                'contato'     => 'Patricia Nunes',
                'site'        => 'globalfix.com.br',
                'observacoes' => 'Telas e baterias com garantia de 6 meses.',
                'ativo'       => true,
            ],
            [
                'nome'        => 'MercadoPeças Online',
                'telefone'    => null,
                'email'       => 'suporte@mercadopecas.com.br',
                'contato'     => null,
                'site'        => 'mercadopecas.com.br',
                'observacoes' => 'Compras online. Verificar avaliações do vendedor antes de comprar.',
                'ativo'       => true,
            ],
            [
                'nome'        => 'Fornecedor Antigo Ltda',
                'telefone'    => '(11) 2222-3333',
                'email'       => 'contato@fornecedorantigo.com.br',
                'contato'     => 'José Antônio',
                'site'        => null,
                'observacoes' => 'Não trabalha mais com este fornecedor.',
                'ativo'       => false,
            ],
        ];

        foreach ($fornecedores as $fornecedor) {
            Fornecedor::updateOrCreate(
                ['email' => $fornecedor['email']],
                $fornecedor
            );
        }
    }
}