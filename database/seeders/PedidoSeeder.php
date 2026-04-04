<?php

namespace Database\Seeders;

use App\Models\Pedido;
use App\Models\Estoque;
use App\Models\Fornecedor;
use App\Models\User;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $joao      = User::where('email', 'joao@servicofacil.com')->first();
        $ana       = User::where('email', 'ana@servicofacil.com')->first();
        $carlos    = User::where('email', 'carlos@servicofacil.com')->first();
        $fernanda  = User::where('email', 'fernanda@servicofacil.com')->first();

        $techparts  = Fornecedor::where('nome', 'like', 'TechParts%')->first();
        $pecasphone = Fornecedor::where('nome', 'like', 'PeçasPhone%')->first();
        $distsul    = Fornecedor::where('nome', 'like', 'Distribuidora Sul%')->first();

        $telaiph13  = Estoque::where('sku', 'IPH13-TELA-ORI')->first();
        $batsama54  = Estoque::where('sku', 'SAM-A54-BAT')->first();
        $pasta      = Estoque::where('sku', 'PASTA-ARCTIC-MX4')->first();
        $ssd        = Estoque::where('sku', 'SSD-SATA-240')->first();
        $ram        = Estoque::where('sku', 'RAM-DDR4-8GB')->first();
        $tela54     = Estoque::where('sku', 'SAM-A54-TELA')->first();

        $pedidos = [
            // Pendentes
            [
                'estoque_id'       => $telaiph13?->id,
                'fornecedor_id'    => $techparts?->id,
                'solicitado_por'   => $ana?->id,
                'recebido_por'     => null,
                'descricao'        => 'Tela iPhone 13 Original',
                'quantidade'       => 3,
                'preco_unitario'   => null,
                'status'           => 'pendente',
                'numero_pedido'    => null,
                'data_solicitacao' => now()->subDays(2)->toDateString(),
                'data_pedido'      => null,
                'data_previsao'    => null,
                'data_recebimento' => null,
                'observacao'       => 'Urgente! Temos cliente aguardando.',
            ],
            [
                'estoque_id'       => $batsama54?->id,
                'fornecedor_id'    => $pecasphone?->id,
                'solicitado_por'   => $joao?->id,
                'recebido_por'     => null,
                'descricao'        => 'Bateria Samsung Galaxy A54',
                'quantidade'       => 5,
                'preco_unitario'   => null,
                'status'           => 'pendente',
                'numero_pedido'    => null,
                'data_solicitacao' => now()->subDays(1)->toDateString(),
                'data_pedido'      => null,
                'data_previsao'    => null,
                'data_recebimento' => null,
                'observacao'       => null,
            ],

            // Pedidos (enviados ao fornecedor)
            [
                'estoque_id'       => $pasta?->id,
                'fornecedor_id'    => $distsul?->id,
                'solicitado_por'   => $carlos?->id,
                'recebido_por'     => null,
                'descricao'        => 'Pasta Térmica Arctic MX-4',
                'quantidade'       => 10,
                'preco_unitario'   => null,
                'status'           => 'pedido',
                'numero_pedido'    => 'PED-2026-001',
                'data_solicitacao' => now()->subDays(5)->toDateString(),
                'data_pedido'      => now()->subDays(3)->toDateString(),
                'data_previsao'    => now()->addDays(2)->toDateString(),
                'data_recebimento' => null,
                'observacao'       => 'Pedido feito por e-mail. Aguardando confirmação.',
            ],
            [
                'estoque_id'       => $ssd?->id,
                'fornecedor_id'    => $techparts?->id,
                'solicitado_por'   => $fernanda?->id,
                'recebido_por'     => null,
                'descricao'        => 'SSD SATA 240GB',
                'quantidade'       => 2,
                'preco_unitario'   => null,
                'status'           => 'pedido',
                'numero_pedido'    => 'PED-2026-002',
                'data_solicitacao' => now()->subDays(7)->toDateString(),
                'data_pedido'      => now()->subDays(6)->toDateString(),
                'data_previsao'    => now()->subDays(1)->toDateString(), // atrasado!
                'data_recebimento' => null,
                'observacao'       => 'Previsão já passou. Cobrar o fornecedor.',
            ],

            // Recebidos
            [
                'estoque_id'       => $tela54?->id,
                'fornecedor_id'    => $pecasphone?->id,
                'solicitado_por'   => $ana?->id,
                'recebido_por'     => $ana?->id,
                'descricao'        => 'Tela Samsung Galaxy A54',
                'quantidade'       => 5,
                'preco_unitario'   => 120.00,
                'status'           => 'recebido',
                'numero_pedido'    => 'PED-2026-003',
                'data_solicitacao' => now()->subDays(15)->toDateString(),
                'data_pedido'      => now()->subDays(13)->toDateString(),
                'data_previsao'    => now()->subDays(8)->toDateString(),
                'data_recebimento' => now()->subDays(7)->toDateString(),
                'observacao'       => 'Recebido com nota fiscal.',
            ],
            [
                'estoque_id'       => $ram?->id,
                'fornecedor_id'    => $techparts?->id,
                'solicitado_por'   => $joao?->id,
                'recebido_por'     => $joao?->id,
                'descricao'        => 'Memória RAM DDR4 8GB',
                'quantidade'       => 4,
                'preco_unitario'   => 95.00,
                'status'           => 'recebido',
                'numero_pedido'    => 'PED-2026-004',
                'data_solicitacao' => now()->subDays(20)->toDateString(),
                'data_pedido'      => now()->subDays(18)->toDateString(),
                'data_previsao'    => now()->subDays(12)->toDateString(),
                'data_recebimento' => now()->subDays(10)->toDateString(),
                'observacao'       => null,
            ],

            // Cancelado
            [
                'estoque_id'       => null,
                'fornecedor_id'    => $distsul?->id,
                'solicitado_por'   => $fernanda?->id,
                'recebido_por'     => null,
                'descricao'        => 'Placa mãe Notebook Positivo',
                'quantidade'       => 1,
                'preco_unitario'   => null,
                'status'           => 'cancelado',
                'numero_pedido'    => null,
                'data_solicitacao' => now()->subDays(10)->toDateString(),
                'data_pedido'      => null,
                'data_previsao'    => null,
                'data_recebimento' => null,
                'observacao'       => 'Cliente desistiu do reparo. Pedido cancelado.',
            ],
        ];

        foreach ($pedidos as $pedido) {
            Pedido::create($pedido);
        }
    }
}