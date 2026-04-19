<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Estoque;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Busca usuários e clientes ─────────────────────────────────────
        $tecnicos = User::whereIn('cargo', ['tecnico', 'supervisor'])->get();
        $clientes = Cliente::all();

        if ($tecnicos->isEmpty() || $clientes->isEmpty()) {
            $this->command->warn('Rode AdminSeeder, UserSeeder e ClienteSeeder antes.');
            return;
        }

        // Peças mais comuns do estoque para associar às OS
        $pecas = [
            ['sku' => 'IPH13-TELA-ORI', 'desc' => 'Tela iPhone 13 Original',       'custo' => 180, 'cobrado' => 350],
            ['sku' => 'IPH14-TELA-ORI', 'desc' => 'Tela iPhone 14 Original',       'custo' => 250, 'cobrado' => 480],
            ['sku' => 'IPH12-TELA-ORI', 'desc' => 'Tela iPhone 12 Original',       'custo' => 160, 'cobrado' => 320],
            ['sku' => 'IPH11-TELA-ORI', 'desc' => 'Tela iPhone 11 Original',       'custo' => 130, 'cobrado' => 280],
            ['sku' => 'SAM-A54-TELA',   'desc' => 'Tela Samsung Galaxy A54',       'custo' => 120, 'cobrado' => 280],
            ['sku' => 'SAM-S23-TELA',   'desc' => 'Tela Samsung Galaxy S23',       'custo' => 200, 'cobrado' => 420],
            ['sku' => 'MOT-G84-TELA',   'desc' => 'Tela Motorola Moto G84',        'custo' =>  95, 'cobrado' => 220],
            ['sku' => 'IPH13-BAT',      'desc' => 'Bateria iPhone 13',             'custo' =>  80, 'cobrado' => 150],
            ['sku' => 'IPH14-BAT',      'desc' => 'Bateria iPhone 14',             'custo' => 100, 'cobrado' => 190],
            ['sku' => 'IPH12-BAT',      'desc' => 'Bateria iPhone 12',             'custo' =>  70, 'cobrado' => 140],
            ['sku' => 'SAM-A54-BAT',    'desc' => 'Bateria Samsung Galaxy A54',    'custo' =>  45, 'cobrado' => 120],
            ['sku' => 'SAM-S23-BAT',    'desc' => 'Bateria Samsung Galaxy S23',    'custo' =>  60, 'cobrado' => 140],
            ['sku' => 'MOT-G84-BAT',    'desc' => 'Bateria Motorola Moto G84',     'custo' =>  40, 'cobrado' => 110],
            ['sku' => 'CON-USBC-UNI',   'desc' => 'Conector USB-C',               'custo' =>  12, 'cobrado' =>  60],
            ['sku' => 'CON-LIGHT-UNI',  'desc' => 'Conector Lightning',            'custo' =>  18, 'cobrado' =>  70],
            ['sku' => 'SSD-SATA-240',   'desc' => 'SSD SATA 240GB',               'custo' => 120, 'cobrado' => 220],
            ['sku' => 'SSD-NVME-256',   'desc' => 'SSD M.2 NVMe 256GB',           'custo' => 160, 'cobrado' => 290],
            ['sku' => 'RAM-DDR4-8GB',   'desc' => 'Memória RAM DDR4 8GB',         'custo' =>  95, 'cobrado' => 180],
            ['sku' => 'RAM-SODIMM-8GB', 'desc' => 'Memória SO-DIMM DDR4 8GB',     'custo' =>  98, 'cobrado' => 185],
            ['sku' => 'PASTA-ARCTIC-MX4','desc' => 'Pasta Térmica Arctic MX-4',   'custo' =>  15, 'cobrado' =>  50],
        ];

        // Templates de OS: equipamento + problema + mão de obra
        $templates = [
            // Celulares
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 13',       'problema' => 'Troca de tela trincada',             'mao_obra' => 80,  'pecas_idx' => [0]],
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 14',       'problema' => 'Troca de tela',                      'mao_obra' => 100, 'pecas_idx' => [1]],
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 12',       'problema' => 'Tela com manchas e touch falhando',  'mao_obra' => 80,  'pecas_idx' => [2]],
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 11',       'problema' => 'Troca de tela quebrada',             'mao_obra' => 70,  'pecas_idx' => [3]],
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 13',       'problema' => 'Bateria sem autonomia',              'mao_obra' => 60,  'pecas_idx' => [7]],
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 14',       'problema' => 'Bateria inchada',                    'mao_obra' => 70,  'pecas_idx' => [8]],
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 12',       'problema' => 'Não carrega, bateria fraca',         'mao_obra' => 60,  'pecas_idx' => [9]],
            ['tipo' => 'celular', 'marca' => 'Apple',    'modelo' => 'iPhone 13',       'problema' => 'Conector Lightning com defeito',     'mao_obra' => 90,  'pecas_idx' => [14]],
            ['tipo' => 'celular', 'marca' => 'Samsung',  'modelo' => 'Galaxy A54',      'problema' => 'Troca de tela quebrada',             'mao_obra' => 70,  'pecas_idx' => [4]],
            ['tipo' => 'celular', 'marca' => 'Samsung',  'modelo' => 'Galaxy S23',      'problema' => 'Tela com listras verticais',         'mao_obra' => 90,  'pecas_idx' => [5]],
            ['tipo' => 'celular', 'marca' => 'Samsung',  'modelo' => 'Galaxy A54',      'problema' => 'Bateria descarregando rápido',       'mao_obra' => 50,  'pecas_idx' => [10]],
            ['tipo' => 'celular', 'marca' => 'Samsung',  'modelo' => 'Galaxy S23',      'problema' => 'Bateria inchada',                    'mao_obra' => 60,  'pecas_idx' => [11]],
            ['tipo' => 'celular', 'marca' => 'Samsung',  'modelo' => 'Galaxy A54',      'problema' => 'Conector USB-C com defeito',         'mao_obra' => 60,  'pecas_idx' => [13]],
            ['tipo' => 'celular', 'marca' => 'Motorola', 'modelo' => 'Moto G84',        'problema' => 'Troca de tela',                      'mao_obra' => 60,  'pecas_idx' => [6]],
            ['tipo' => 'celular', 'marca' => 'Motorola', 'modelo' => 'Moto G84',        'problema' => 'Bateria com mau contato',            'mao_obra' => 50,  'pecas_idx' => [12]],
            ['tipo' => 'celular', 'marca' => 'Motorola', 'modelo' => 'Moto G84',        'problema' => 'Conector USB-C solto',               'mao_obra' => 55,  'pecas_idx' => [13]],
            // Notebooks
            ['tipo' => 'notebook', 'marca' => 'Dell',   'modelo' => 'Inspiron 15',     'problema' => 'Lentidão extrema, upgrade de SSD',   'mao_obra' => 120, 'pecas_idx' => [15]],
            ['tipo' => 'notebook', 'marca' => 'Dell',   'modelo' => 'Inspiron 15',     'problema' => 'SSD com setores defeituosos',        'mao_obra' => 100, 'pecas_idx' => [16]],
            ['tipo' => 'notebook', 'marca' => 'HP',     'modelo' => '250 G8',          'problema' => 'Travando com frequência, upgrade RAM','mao_obra' => 80,  'pecas_idx' => [17]],
            ['tipo' => 'notebook', 'marca' => 'HP',     'modelo' => '250 G8',          'problema' => 'Memoria RAM com defeito',            'mao_obra' => 80,  'pecas_idx' => [18]],
            ['tipo' => 'notebook', 'marca' => 'Lenovo', 'modelo' => 'IdeaPad 3',       'problema' => 'Superaquecimento, limpeza e pasta',  'mao_obra' => 90,  'pecas_idx' => [19]],
            ['tipo' => 'notebook', 'marca' => 'Dell',   'modelo' => 'Inspiron 15',     'problema' => 'Superaquecimento, limpeza interna',  'mao_obra' => 100, 'pecas_idx' => [19]],
            // Somente mão de obra (sem peça)
            ['tipo' => 'celular',  'marca' => 'Apple',    'modelo' => 'iPhone 13',     'problema' => 'Formatação e restauração do iOS',    'mao_obra' => 120, 'pecas_idx' => []],
            ['tipo' => 'celular',  'marca' => 'Samsung',  'modelo' => 'Galaxy A54',    'problema' => 'Formatação e remoção de vírus',      'mao_obra' => 100, 'pecas_idx' => []],
            ['tipo' => 'notebook', 'marca' => 'Dell',     'modelo' => 'Inspiron 15',   'problema' => 'Formatação Windows 11',              'mao_obra' => 150, 'pecas_idx' => []],
            ['tipo' => 'notebook', 'marca' => 'HP',       'modelo' => '250 G8',        'problema' => 'Remoção de vírus e otimização',      'mao_obra' => 130, 'pecas_idx' => []],
            ['tipo' => 'notebook', 'marca' => 'Lenovo',   'modelo' => 'IdeaPad 3',     'problema' => 'Formatação e configuração',          'mao_obra' => 140, 'pecas_idx' => []],
        ];

        // ── Geração das OS por mês ────────────────────────────────────────
        // Quantidade de OS por mês: cresce gradualmente até o mês atual
        $meses = [
            Carbon::now()->subMonths(5)->startOfMonth(), // Nov
            Carbon::now()->subMonths(4)->startOfMonth(), // Dez
            Carbon::now()->subMonths(3)->startOfMonth(), // Jan
            Carbon::now()->subMonths(2)->startOfMonth(), // Fev
            Carbon::now()->subMonths(1)->startOfMonth(), // Mar
            Carbon::now()->startOfMonth(),               // Abr (mês atual)
        ];

        $qtdPorMes = [18, 22, 16, 25, 28, 14]; // OS por mês (mês atual menor pois ainda está no início)

        $contador = 1;

        foreach ($meses as $mesIdx => $mesInicio) {
            $mesFim     = $mesInicio->copy()->endOfMonth();
            $isMesAtual = $mesIdx === count($meses) - 1;
            $qtd        = $qtdPorMes[$mesIdx];

            for ($i = 0; $i < $qtd; $i++) {
                $dataEntrada = Carbon::createFromTimestamp(
                    rand($mesInicio->timestamp, min($mesFim->timestamp, now()->timestamp))
                )->toDateString();

                // Status: meses anteriores têm mais entregues; mês atual tem mais em aberto
                if ($isMesAtual) {
                    $status = $this->sortearStatus([
                        'recebido'        => 25,
                        'diagnostico'     => 20,
                        'aguardando_peca' => 20,
                        'em_reparo'       => 20,
                        'pronto'          => 10,
                        'entregue'        =>  5,
                    ]);
                } else {
                    $status = $this->sortearStatus([
                        'recebido'        =>  2,
                        'diagnostico'     =>  3,
                        'aguardando_peca' =>  5,
                        'em_reparo'       =>  5,
                        'pronto'          => 10,
                        'entregue'        => 75,
                    ]);
                }

                $prioridade = $this->sortearStatus([
                    'baixa'  => 15,
                    'normal' => 60,
                    'alta'   => 20,
                    'urgente'=> 5,
                ]);

                $template   = $templates[array_rand($templates)];
                $cliente    = $clientes->random();
                $tecnico    = $tecnicos->random();
                $supervisor = $tecnicos->where('cargo', 'supervisor')->first();

                // Calcula datas de acordo com o status
                $dataConclusao = null;
                $dataEntrega   = null;
                $dataPrevisao  = Carbon::parse($dataEntrada)->addDays(rand(3, 10))->toDateString();

                if (in_array($status, ['pronto', 'entregue'])) {
                    $dataConclusao = Carbon::parse($dataEntrada)->addDays(rand(1, 7))->toDateString();
                }
                if ($status === 'entregue') {
                    $dataEntrega = Carbon::parse($dataConclusao)->addDays(rand(1, 5))->toDateString();
                }

                // Valor: peças + mão de obra
                $custoTotal   = 0;
                $valorCobrado = 0;

                if (in_array($status, ['em_reparo', 'pronto', 'entregue'])) {
                    $valorCobrado += $template['mao_obra'];
                    $custoTotal   += $template['mao_obra'] * 0.1; // custo indireto mão de obra
                }

                // Monta número da OS
                $numero = 'OS-' . str_pad($contador, 5, '0', STR_PAD_LEFT);
                $contador++;

                // Insere servico
                $servicoId = DB::table('servicos')->insertGetId([
                    'numero'            => $numero,
                    'cliente_id'        => $cliente->id,
                    'supervisor_id'     => $supervisor?->id,
                    'tipo'              => $template['tipo'],
                    'status'            => $status,
                    'prioridade'        => $prioridade,
                    'obs_internas'      => null,
                    'obs_cliente'       => null,
                    'valor_cobrado'     => 0, // atualizado depois
                    'custo_total'       => 0,
                    'data_entrada'      => $dataEntrada,
                    'data_previsao'     => $dataPrevisao,
                    'data_conclusao'    => $dataConclusao,
                    'data_entrega'      => $dataEntrega,
                    'validade_orcamento'=> null,
                    'created_at'        => $dataEntrada,
                    'updated_at'        => $dataEntrega ?? $dataConclusao ?? $dataEntrada,
                ]);

                // Insere equipamento
                $equipamentoId = DB::table('servico_equipamentos')->insertGetId([
                    'servico_id'      => $servicoId,
                    'tipo'            => $template['tipo'],
                    'marca'           => $template['marca'],
                    'modelo'          => $template['modelo'],
                    'numero_serie'    => null,
                    'condicao_entrada'=> $template['problema'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // Insere peças (somente se status avançou o suficiente)
                if (in_array($status, ['em_reparo', 'pronto', 'entregue']) && !empty($template['pecas_idx'])) {
                    foreach ($template['pecas_idx'] as $pi) {
                        $peca       = $pecas[$pi];
                        $estoqueRec = Estoque::where('sku', $peca['sku'])->first();

                        DB::table('servico_pecas')->insert([
                            'servico_equipamento_id' => $equipamentoId,
                            'estoque_id'             => $estoqueRec?->id,
                            'descricao'              => $peca['desc'],
                            'quantidade'             => 1,
                            'preco_custo'            => $peca['custo'],
                            'preco_cobrado'          => $peca['cobrado'],
                            'created_at'             => now(),
                            'updated_at'             => now(),
                        ]);

                        $custoTotal   += $peca['custo'];
                        $valorCobrado += $peca['cobrado'];
                    }

                    // Adiciona mão de obra separado (já somado antes se sem peças)
                    // Reajusta: mão de obra só entra uma vez
                    $valorCobrado = $valorCobrado > 0
                        ? $valorCobrado + $template['mao_obra']
                        : $template['mao_obra'];
                    $custoTotal   = $custoTotal > 0
                        ? $custoTotal + ($template['mao_obra'] * 0.1)
                        : $template['mao_obra'] * 0.1;
                }

                // Para OS só de mão de obra (sem peças)
                if (in_array($status, ['em_reparo', 'pronto', 'entregue']) && empty($template['pecas_idx'])) {
                    $valorCobrado = $template['mao_obra'];
                    $custoTotal   = $template['mao_obra'] * 0.1;
                }

                // Atualiza valores na OS
                DB::table('servicos')->where('id', $servicoId)->update([
                    'valor_cobrado' => $valorCobrado,
                    'custo_total'   => $custoTotal,
                ]);

                // Insere técnico responsável
                DB::table('servico_users')->insert([
                    'servico_id'  => $servicoId,
                    'user_id'     => $tecnico->id,
                    'papel'       => 'tecnico',
                    'data_inicio' => $dataEntrada,
                    'data_fim'    => $dataConclusao,
                    'observacao'  => null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // Insere histórico de status
                DB::table('servico_status')->insert([
                    'servico_id'      => $servicoId,
                    'user_id'         => $tecnico->id,
                    'status_anterior' => null,
                    'status_novo'     => 'recebido',
                    'observacao'      => 'OS aberta.',
                    'created_at'      => $dataEntrada,
                    'updated_at'      => $dataEntrada,
                ]);

                if ($status !== 'recebido') {
                    DB::table('servico_status')->insert([
                        'servico_id'      => $servicoId,
                        'user_id'         => $tecnico->id,
                        'status_anterior' => 'recebido',
                        'status_novo'     => $status,
                        'observacao'      => null,
                        'created_at'      => $dataConclusao ?? $dataEntrada,
                        'updated_at'      => $dataConclusao ?? $dataEntrada,
                    ]);
                }
            }
        }

        $total = $contador - 1;
        $this->command->info("✓ {$total} OS criadas com dados dos últimos 6 meses.");
    }

    private function sortearStatus(array $pesos): string
    {
        $total = array_sum($pesos);
        $rand  = rand(1, $total);
        $acum  = 0;
        foreach ($pesos as $status => $peso) {
            $acum += $peso;
            if ($rand <= $acum) return $status;
        }
        return array_key_first($pesos);
    }
}