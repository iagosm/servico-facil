<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use App\Models\ServicoPeca;
use App\Models\ServicoUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceiroController extends Controller
{
    public function index(Request $request)
    {
        $inicio = $request->input('data_inicio', now()->startOfMonth()->toDateString());
        $fim    = $request->input('data_fim',    now()->toDateString());

        // Base query — apenas OS concluídas ou entregues geram receita
        $base = Servico::whereBetween('data_conclusao', [$inicio, $fim])
            ->whereIn('status', ['pronto', 'entregue']);

        // ── Métricas ────────────────────────────────────────────────
        $receitaTotal = (clone $base)->sum('valor_cobrado');
        $custoTotal   = (clone $base)->sum('custo_total');
        $osConcluidas = (clone $base)->count();
        $ticketMedio  = $osConcluidas > 0
            ? round($receitaTotal / $osConcluidas, 2)
            : 0;

        $osAbertas = Servico::whereNotIn('status', ['entregue'])->count();

        // ── Receita vs Custo por mês ─────────────────────────────────
        $porMes = (clone $base)
            ->select(
                DB::raw('MONTH(data_conclusao) as mes'),
                DB::raw('YEAR(data_conclusao)  as ano'),
                DB::raw('SUM(valor_cobrado)    as receita'),
                DB::raw('SUM(custo_total)      as custo'),
            )
            ->groupBy('ano', 'mes')
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();

        // ── OS por tipo ──────────────────────────────────────────────
        $porTipo = (clone $base)
            ->select('tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        // ── OS por status (snapshot atual, sem filtro de data) ───────
        $porStatus = Servico::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Ranking de técnicos ──────────────────────────────────────
        $rankingTecnicos = ServicoUser::query()
            ->join('servicos', 'servico_users.servico_id', '=', 'servicos.id')
            ->join('users',    'servico_users.user_id',    '=', 'users.id')
            ->whereNotNull('servicos.data_conclusao')
            ->whereBetween('servicos.data_conclusao', [$inicio, $fim])
            ->whereIn('servicos.status', ['pronto', 'entregue'])
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT servicos.id)      as total_os'),
                DB::raw('SUM(servicos.valor_cobrado)      as receita_gerada'),
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('receita_gerada')
            ->get();

        // ── Peças mais usadas ────────────────────────────────────────
        $pecasMaisUsadas = ServicoPeca::query()
            ->join('servico_equipamentos', 'servico_pecas.servico_equipamento_id', '=', 'servico_equipamentos.id')
            ->join('servicos', 'servico_equipamentos.servico_id', '=', 'servicos.id')
            ->whereBetween('servicos.data_conclusao', [$inicio, $fim])
            ->whereIn('servicos.status', ['pronto', 'entregue'])
            ->select(
                'servico_pecas.descricao',
                DB::raw('SUM(servico_pecas.quantidade) as total_usado'),
                DB::raw('SUM(servico_pecas.preco_custo * servico_pecas.quantidade) as custo_total'),
            )
            ->groupBy('servico_pecas.descricao')
            ->orderByDesc('total_usado')
            ->limit(8)
            ->get();

        return inertia('financeiro/Index', [
            'metricas' => [
                'receita_total' => $receitaTotal,
                'custo_total'   => $custoTotal,
                'lucro_liquido' => $receitaTotal - $custoTotal,
                'os_concluidas' => $osConcluidas,
                'ticket_medio'  => $ticketMedio,
                'os_abertas'    => $osAbertas,
                'margem'        => $receitaTotal > 0
                    ? round((($receitaTotal - $custoTotal) / $receitaTotal) * 100, 1)
                    : 0,
            ],
            'por_mes'          => $porMes,
            'por_tipo'         => $porTipo,
            'por_status'       => $porStatus,
            'ranking_tecnicos' => $rankingTecnicos,
            'pecas_mais_usadas'=> $pecasMaisUsadas,
            'filters'          => [
                'data_inicio' => $inicio,
                'data_fim'    => $fim,
            ],
        ]);
    }
}