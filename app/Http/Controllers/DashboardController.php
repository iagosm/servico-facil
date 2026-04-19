<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoje      = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes    = Carbon::now()->endOfMonth();

        return inertia('Dashboard', [
            'cards'            => $this->cards($hoje, $inicioMes, $fimMes),
            'faturamentoMeses' => $this->faturamentoUltimosMeses(),
            'osPorStatus'      => $this->osPorStatus(),
            'ultimasOs'        => $this->ultimasOs(),
            'alertaEstoque'    => $this->alertaEstoque(),
            'topTecnicos'      => $this->topTecnicos($inicioMes, $fimMes),
        ]);
    }

    // ── Cards ─────────────────────────────────────────────────────────────────

    private function cards(Carbon $hoje, Carbon $inicioMes, Carbon $fimMes): array
    {
        $inicioMesAnterior = $inicioMes->copy()->subMonth()->startOfMonth();
        $fimMesAnterior    = $inicioMes->copy()->subMonth()->endOfMonth();

        // OS abertas hoje (entrada hoje, não entregues)
        $osAbertasHoje = Servico::whereDate('data_entrada', $hoje)
            ->whereNotIn('status', ['entregue'])
            ->count();

        // OS prontas para retirada
        $osProntasRetirada = Servico::where('status', 'pronto')->count();

        // Faturamento e lucro — mês atual
        $mesAtual = Servico::whereBetween('data_conclusao', [$inicioMes, $fimMes])
            ->whereIn('status', ['pronto', 'entregue'])
            ->selectRaw('
                COALESCE(SUM(valor_cobrado), 0)              as faturamento,
                COALESCE(SUM(valor_cobrado - custo_total), 0) as lucro
            ')
            ->first();

        // Faturamento e lucro — mês anterior (para calcular delta)
        $mesAnterior = Servico::whereBetween('data_conclusao', [$inicioMesAnterior, $fimMesAnterior])
            ->whereIn('status', ['pronto', 'entregue'])
            ->selectRaw('
                COALESCE(SUM(valor_cobrado), 0)              as faturamento,
                COALESCE(SUM(valor_cobrado - custo_total), 0) as lucro
            ')
            ->first();

        $fatAtual      = (float) ($mesAtual->faturamento  ?? 0);
        $lucroAtual    = (float) ($mesAtual->lucro        ?? 0);
        $fatAnterior   = (float) ($mesAnterior->faturamento ?? 0);
        $lucroAnterior = (float) ($mesAnterior->lucro       ?? 0);

        // Delta percentual — null se não houver referência no mês anterior
        $fatDelta   = $fatAnterior   > 0 ? round((($fatAtual   - $fatAnterior)   / $fatAnterior)   * 100) : null;
        $lucroDelta = $lucroAnterior > 0 ? round((($lucroAtual - $lucroAnterior) / $lucroAnterior) * 100) : null;

        // Itens com estoque abaixo do mínimo
        $estoqueBaixoCount = Estoque::whereColumn('quantidade', '<=', 'quantidade_minima')
            ->where('quantidade_minima', '>', 0)
            ->count();

        return [
            'os_abertas_hoje'    => $osAbertasHoje,
            'os_prontas_retirada'=> $osProntasRetirada,
            'faturamento_mes'    => $fatAtual,
            'lucro_mes'          => $lucroAtual,
            'estoque_baixo_count'=> $estoqueBaixoCount,
            'faturamento_delta'  => $fatDelta,
            'lucro_delta'        => $lucroDelta,
        ];
    }

    // ── Gráfico — últimos 6 meses ─────────────────────────────────────────────

    private function faturamentoUltimosMeses(): array
    {
        return collect(range(5, 0))->map(function ($i) {
            $mes = Carbon::now()->subMonths($i);

            $dados = Servico::whereYear('data_conclusao', $mes->year)
                ->whereMonth('data_conclusao', $mes->month)
                ->whereIn('status', ['pronto', 'entregue'])
                ->selectRaw('
                    COALESCE(SUM(valor_cobrado), 0)              as faturamento,
                    COALESCE(SUM(valor_cobrado - custo_total), 0) as lucro
                ')
                ->first();

            return [
                'mes'        => ucfirst($mes->translatedFormat('M/y')),
                'faturamento'=> round((float) ($dados->faturamento ?? 0), 2),
                'lucro'      => round((float) ($dados->lucro       ?? 0), 2),
            ];
        })->values()->all();
    }

    // ── OS por Status (snapshot atual, sem filtro de data) ────────────────────

    private function osPorStatus(): array
    {
        $statusList = ['recebido', 'diagnostico', 'aguardando_peca', 'em_reparo', 'pronto'];

        $contagens = Servico::whereIn('status', $statusList)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect($statusList)->map(fn ($s) => [
            'status' => $s,
            'total'  => (int) ($contagens[$s] ?? 0),
        ])->values()->all();
    }

    // ── Últimas OS ────────────────────────────────────────────────────────────

    private function ultimasOs(): array
    {
        return Servico::with(['cliente:id,nome', 'equipamentos:id,servico_id,tipo,marca,modelo'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(fn ($os) => [
                'id'          => $os->id,
                'numero'      => $os->numero,
                'status'      => $os->status,
                'prioridade'  => $os->prioridade,
                'cliente_nome'=> $os->cliente?->nome,
                'equipamento' => trim(($os->equipamentos->first()?->marca ?? '')
                                 .' '.($os->equipamentos->first()?->modelo ?? '')),
                'data_entrada'=> $os->data_entrada?->format('d/m/Y'),
                'valor'       => (float) $os->valor_cobrado,
            ])
            ->all();
    }

    // ── Alerta de Estoque ─────────────────────────────────────────────────────

    private function alertaEstoque(): array
    {
        return Estoque::whereColumn('quantidade', '<=', 'quantidade_minima')
            ->where('quantidade_minima', '>', 0)
            ->orderByRaw('(quantidade / quantidade_minima) ASC')
            ->limit(8)
            ->get(['id', 'nome', 'sku', 'quantidade', 'quantidade_minima'])
            ->map(fn ($item) => [
                'id'               => $item->id,
                'nome'             => $item->nome,
                'sku'              => $item->sku,
                'quantidade'       => $item->quantidade,
                'quantidade_minima'=> $item->quantidade_minima,
            ])
            ->all();
    }

    // ── Top Técnicos ──────────────────────────────────────────────────────────

    private function topTecnicos(Carbon $inicioMes, Carbon $fimMes): array
    {
        // Aceita qualquer papel — o seeder usa 'tecnico', futuras OS podem ter 'executor'
        return DB::table('servico_users')
            ->join('servicos', 'servicos.id', '=', 'servico_users.servico_id')
            ->join('users',    'users.id',    '=', 'servico_users.user_id')
            ->whereIn('servicos.status', ['pronto', 'entregue'])
            ->whereBetween('servicos.data_conclusao', [$inicioMes, $fimMes])
            ->select(
                'users.id',
                'users.name',
                'users.cargo',
                DB::raw('COUNT(DISTINCT servicos.id) as os_concluidas'),
                DB::raw('SUM(servicos.valor_cobrado) as faturamento_gerado'),
            )
            ->groupBy('users.id', 'users.name', 'users.cargo')
            ->orderByDesc('os_concluidas')
            ->limit(5)
            ->get()
            ->map(fn ($u) => [
                'id'               => $u->id,
                'name'             => $u->name,
                'cargo'            => $u->cargo,
                'os_concluidas'    => (int)   $u->os_concluidas,
                'faturamento_gerado'=> (float) $u->faturamento_gerado,
            ])
            ->all();
    }
}