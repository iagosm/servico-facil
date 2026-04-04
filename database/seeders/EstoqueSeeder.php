<?php

namespace Database\Seeders;

use App\Models\Estoque;
use Illuminate\Database\Seeder;

class EstoqueSeeder extends Seeder
{
    public function run(): void
    {
        $itens = [
            // Telas
            [
                'nome'             => 'Tela iPhone 13 Original',
                'descricao'        => 'Display LCD original Apple para iPhone 13. Com digitalizador.',
                'sku'              => 'IPH13-TELA-ORI',
                'quantidade'       => 0,
                'quantidade_minima'=> 2,
                'preco_custo'      => 180.00,
                'preco_venda'      => 350.00,
            ],
            [
                'nome'             => 'Tela Samsung Galaxy A54',
                'descricao'        => 'Display AMOLED para Samsung Galaxy A54. Com moldura.',
                'sku'              => 'SAM-A54-TELA',
                'quantidade'       => 4,
                'quantidade_minima'=> 2,
                'preco_custo'      => 120.00,
                'preco_venda'      => 280.00,
            ],
            [
                'nome'             => 'Tela Motorola Moto G84',
                'descricao'        => 'Display POLED para Moto G84. Original.',
                'sku'              => 'MOT-G84-TELA',
                'quantidade'       => 3,
                'quantidade_minima'=> 2,
                'preco_custo'      => 95.00,
                'preco_venda'      => 220.00,
            ],
            [
                'nome'             => 'Tela Notebook Dell 15.6"',
                'descricao'        => 'Tela LCD 15.6 polegadas Full HD para notebooks Dell.',
                'sku'              => 'DELL-TELA-156',
                'quantidade'       => 1,
                'quantidade_minima'=> 1,
                'preco_custo'      => 210.00,
                'preco_venda'      => 420.00,
            ],

            // Baterias
            [
                'nome'             => 'Bateria iPhone 13',
                'descricao'        => 'Bateria de reposição para iPhone 13. 3227 mAh.',
                'sku'              => 'IPH13-BAT',
                'quantidade'       => 5,
                'quantidade_minima'=> 3,
                'preco_custo'      => 80.00,
                'preco_venda'      => 150.00,
            ],
            [
                'nome'             => 'Bateria Samsung Galaxy A54',
                'descricao'        => 'Bateria original para Samsung Galaxy A54. 5000 mAh.',
                'sku'              => 'SAM-A54-BAT',
                'quantidade'       => 1,
                'quantidade_minima'=> 3,
                'preco_custo'      => 45.00,
                'preco_venda'      => 120.00,
            ],
            [
                'nome'             => 'Bateria Notebook Dell 11.1V',
                'descricao'        => 'Bateria para notebooks Dell Inspiron série 3000/5000.',
                'sku'              => 'DELL-BAT-111',
                'quantidade'       => 2,
                'quantidade_minima'=> 1,
                'preco_custo'      => 130.00,
                'preco_venda'      => 280.00,
            ],

            // Conectores e cabos
            [
                'nome'             => 'Conector de Carga USB-C Universal',
                'descricao'        => 'Conector de carga USB-C compatível com diversos modelos Android.',
                'sku'              => 'CON-USBC-UNI',
                'quantidade'       => 15,
                'quantidade_minima'=> 5,
                'preco_custo'      => 12.00,
                'preco_venda'      => 35.00,
            ],
            [
                'nome'             => 'Cabo Flex Samsung (modelo variado)',
                'descricao'        => 'Cabo flat/flex para reparo de placas Samsung.',
                'sku'              => 'SAM-FLEX-001',
                'quantidade'       => 8,
                'quantidade_minima'=> 5,
                'preco_custo'      => 25.00,
                'preco_venda'      => 60.00,
            ],

            // Componentes eletrônicos
            [
                'nome'             => 'Capacitor Eletrolítico 1000µF 25V',
                'descricao'        => 'Capacitor eletrolítico 1000µF 25V. Para reparo de fontes e TVs.',
                'sku'              => 'CAP-1000UF-25V',
                'quantidade'       => 50,
                'quantidade_minima'=> 10,
                'preco_custo'      => 0.50,
                'preco_venda'      => 3.00,
            ],
            [
                'nome'             => 'Transistor BC548',
                'descricao'        => 'Transistor NPN BC548 de uso geral.',
                'sku'              => 'TRANS-BC548',
                'quantidade'       => 100,
                'quantidade_minima'=> 20,
                'preco_custo'      => 0.30,
                'preco_venda'      => 2.00,
            ],
            [
                'nome'             => 'Fusível 5A 250V',
                'descricao'        => 'Fusível de vidro 5A 250V. Para fontes e eletrodomésticos.',
                'sku'              => 'FUS-5A-250V',
                'quantidade'       => 30,
                'quantidade_minima'=> 10,
                'preco_custo'      => 0.80,
                'preco_venda'      => 4.00,
            ],

            // Insumos
            [
                'nome'             => 'Pasta Térmica Arctic MX-4',
                'descricao'        => 'Pasta térmica de alta performance para CPUs e GPUs.',
                'sku'              => 'PASTA-ARCTIC-MX4',
                'quantidade'       => 2,
                'quantidade_minima'=> 5,
                'preco_custo'      => 15.00,
                'preco_venda'      => 35.00,
            ],
            [
                'nome'             => 'Álcool Isopropílico 99% 500ml',
                'descricao'        => 'Álcool isopropílico para limpeza de placas e componentes.',
                'sku'              => 'ALC-ISO-500ML',
                'quantidade'       => 3,
                'quantidade_minima'=> 2,
                'preco_custo'      => 18.00,
                'preco_venda'      => 40.00,
            ],
            [
                'nome'             => 'Fita Adesiva Dupla Face 3M',
                'descricao'        => 'Fita dupla face para fixação de telas e componentes.',
                'sku'              => 'FITA-3M-DUP',
                'quantidade'       => 10,
                'quantidade_minima'=> 3,
                'preco_custo'      => 8.00,
                'preco_venda'      => 20.00,
            ],

            // SSD / HD
            [
                'nome'             => 'SSD SATA 240GB',
                'descricao'        => 'SSD SATA III 240GB. Para upgrade de notebooks e desktops.',
                'sku'              => 'SSD-SATA-240',
                'quantidade'       => 3,
                'quantidade_minima'=> 1,
                'preco_custo'      => 120.00,
                'preco_venda'      => 220.00,
            ],
            [
                'nome'             => 'Memória RAM DDR4 8GB 2666MHz',
                'descricao'        => 'Pente de memória RAM DDR4 8GB para notebooks e desktops.',
                'sku'              => 'RAM-DDR4-8GB',
                'quantidade'       => 4,
                'quantidade_minima'=> 2,
                'preco_custo'      => 95.00,
                'preco_venda'      => 180.00,
            ],
        ];

        foreach ($itens as $item) {
            Estoque::updateOrCreate(
                ['sku' => $item['sku']],
                $item
            );
        }
    }
}