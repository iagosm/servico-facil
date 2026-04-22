<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Estoque;
use App\Models\Servico;
use App\Models\ServicoStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::query()
            ->with(['cliente', 'supervisor'])
            ->when(request('search'), fn($q, $s) =>
                $q->where('numero', 'like', "%{$s}%")
                  ->orWhereHas('cliente', fn($q2) => $q2->where('nome', 'like', "%{$s}%"))
            )
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->when(request('prioridade'), fn($q, $p) => $q->where('prioridade', $p))
            ->latest()
            ->paginate(request('perPage', 15))
            ->withQueryString();

        return inertia('servicos/Index', [
            'servicos' => $servicos,
            'filters'  => request()->only('search', 'status', 'prioridade', 'perPage'),
        ]);
    }

    public function create()
    {
        return inertia('servicos/Create', [
            'clientes'    => Cliente::orderBy('nome')->get(['id', 'nome', 'telefone', 'documento']),
            'tecnicos'    => User::where('ativo', true)
                                 ->whereIn('cargo', ['tecnico', 'supervisor', 'gerente', 'admin'])
                                 ->orderBy('name')
                                 ->get(['id', 'name', 'cargo']),
            'estoque'     => Estoque::where('quantidade', '>', 0)->orderBy('nome')->get(['id', 'nome', 'sku', 'preco_venda']),
            'supervisores' => User::where('ativo', true)
                                  ->whereIn('cargo', ['supervisor', 'gerente', 'admin'])
                                  ->orderBy('name')
                                  ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'                              => 'required|exists:clientes,id',
            'tipo'                                    => 'required|in:diagnostico,reparo,orcamento',
            'prioridade'                              => 'required|in:normal,urgente,aguardando_aprovacao',
            'supervisor_id'                           => 'nullable|exists:users,id',
            'data_previsao'                           => 'nullable|date',
            'valor_cobrado'                           => 'nullable|numeric|min:0',
            'obs_internas'                            => 'nullable|string|max:2000',
            'obs_cliente'                             => 'nullable|string|max:2000',
            'validade_orcamento'                      => 'nullable|date',

            // Equipamentos
            'equipamentos'                            => 'required|array|min:1',
            'equipamentos.*.tipo'                     => 'required|string|max:100',
            'equipamentos.*.marca'                    => 'nullable|string|max:100',
            'equipamentos.*.modelo'                   => 'nullable|string|max:100',
            'equipamentos.*.numero_serie'             => 'nullable|string|max:100',
            'equipamentos.*.condicao_entrada'         => 'nullable|string|max:500',

            // Problemas por equipamento
            'equipamentos.*.problemas'                => 'nullable|array',
            'equipamentos.*.problemas.*.descricao'    => 'required|string|max:500',

            // Itens do cliente
            'itens_cliente'                           => 'nullable|array',
            'itens_cliente.*.descricao'               => 'required|string|max:200',

            // Técnicos
            'tecnicos'                                => 'nullable|array',
            'tecnicos.*.user_id'                      => 'required|exists:users,id',
            'tecnicos.*.papel'                        => 'required|in:executor,supervisor,auxiliar',
        ]);

        DB::transaction(function () use ($request) {
            $numero = 'OS-' . str_pad(Servico::count() + 1, 4, '0', STR_PAD_LEFT);

            $servico = Servico::create([
                'numero'             => $numero,
                'cliente_id'         => $request->cliente_id,
                'supervisor_id'      => $request->supervisor_id,
                'tipo'               => $request->tipo,
                'status'             => 'recebido',
                'prioridade'         => $request->prioridade,
                'obs_internas'       => $request->obs_internas,
                'obs_cliente'        => $request->obs_cliente,
                'valor_cobrado'      => $request->valor_cobrado,
                'data_entrada'       => now(),
                'data_previsao'      => $request->data_previsao,
                'validade_orcamento' => $request->validade_orcamento,
            ]);

            // Equipamentos e seus relacionamentos
            foreach ($request->equipamentos as $equipData) {
                $equip = $servico->equipamentos()->create([
                    'tipo'             => $equipData['tipo'],
                    'marca'            => $equipData['marca'] ?? null,
                    'modelo'           => $equipData['modelo'] ?? null,
                    'numero_serie'     => $equipData['numero_serie'] ?? null,
                    'condicao_entrada' => $equipData['condicao_entrada'] ?? null,
                ]);

                foreach ($equipData['problemas'] ?? [] as $prob) {
                    $equip->problemas()->create([
                        'descricao'    => $prob['descricao'],
                        'resolvido'    => false,
                    ]);
                }
            }

            // Itens do cliente
            foreach ($request->itens_cliente ?? [] as $item) {
                $servico->itensCliente()->create(['descricao' => $item['descricao']]);
            }

            // Técnicos
            foreach ($request->tecnicos ?? [] as $tec) {
                $servico->users()->attach($tec['user_id'], ['papel' => $tec['papel']]);
            }

            // Registro de status inicial
            ServicoStatus::create([
                'servico_id'     => $servico->id,
                'user_id'        => Auth::id(),
                'status_anterior' => null,
                'status_novo'    => 'recebido',
                'observacao'     => 'OS criada',
            ]);
        });

        return to_route('servicos.index')->with('sucesso', 'Ordem de Serviço criada com sucesso!');
    }

    public function show(Servico $servico)
    {
        $servico->load([
            'cliente',
            'supervisor',
            'equipamentos.problemas',
            'equipamentos.pecas.estoque',
            'itensCliente',
            'users',
            'statusTimeline.user',
        ]);

        return inertia('servicos/Show', [
            'servico'     => $servico,
            'tecnicos'    => User::where('ativo', true)
                                 ->whereIn('cargo', ['tecnico', 'supervisor', 'gerente', 'admin'])
                                 ->orderBy('name')
                                 ->get(['id', 'name', 'cargo']),
            'estoque'     => Estoque::where('quantidade', '>', 0)->orderBy('nome')->get(['id', 'nome', 'sku', 'preco_venda']),
        ]);
    }

    public function edit(Servico $servico)
    {
        $servico->load(['equipamentos.problemas', 'equipamentos.pecas', 'itensCliente', 'users']);

        return inertia('servicos/Edit', [
            'servico'      => $servico,
            'clientes'     => Cliente::orderBy('nome')->get(['id', 'nome', 'telefone', 'documento']),
            'tecnicos'     => User::where('ativo', true)->whereIn('cargo', ['tecnico', 'supervisor', 'gerente', 'admin'])->orderBy('name')->get(['id', 'name', 'cargo']),
            'estoque'      => Estoque::where('quantidade', '>', 0)->orderBy('nome')->get(['id', 'nome', 'sku', 'preco_venda']),
            'supervisores' => User::where('ativo', true)->whereIn('cargo', ['supervisor', 'gerente', 'admin'])->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Servico $servico)
    {
        $request->validate([
            'tipo'               => 'required|in:diagnostico,reparo,orcamento',
            'prioridade'         => 'required|in:normal,urgente,aguardando_aprovacao',
            'supervisor_id'      => 'nullable|exists:users,id',
            'data_previsao'      => 'nullable|date',
            'valor_cobrado'      => 'nullable|numeric|min:0',
            'obs_internas'       => 'nullable|string|max:2000',
            'obs_cliente'        => 'nullable|string|max:2000',
            'validade_orcamento' => 'nullable|date',
        ]);

        $servico->update($request->only([
            'tipo', 'prioridade', 'supervisor_id',
            'data_previsao', 'valor_cobrado',
            'obs_internas', 'obs_cliente', 'validade_orcamento',
        ]));

        return to_route('servicos.show', $servico)->with('sucesso', 'OS atualizada com sucesso!');
    }

    public function destroy(Servico $servico)
    {
        $servico->delete();
        return to_route('servicos.index')->with('sucesso', 'OS removida com sucesso!');
    }

    /**
     * Atualiza status da OS com registro na timeline.
     */
    public function updateStatus(Request $request, Servico $servico)
    {
        $request->validate([
            'status'     => 'required|in:recebido,diagnostico,aguardando_peca,em_reparo,pronto,entregue',
            'observacao' => 'nullable|string|max:500',
        ]);

        $statusAnterior = $servico->status;
        $statusNovo     = $request->status;

        $servico->status = $statusNovo;

        if ($statusNovo === 'pronto' && !$servico->data_conclusao) {
            $servico->data_conclusao = now();
        }

        if ($statusNovo === 'entregue' && !$servico->data_entrega) {
            $servico->data_entrega = now();
        }

        $servico->save();

        ServicoStatus::create([
            'servico_id'      => $servico->id,
            'user_id'         => Auth::id(),
            'status_anterior' => $statusAnterior,
            'status_novo'     => $statusNovo,
            'observacao'      => $request->observacao,
        ]);

        return back()->with('sucesso', 'Status atualizado com sucesso!');
    }

    /**
     * Criação rápida de cliente (chamado via Inertia do formulário de OS).
     */
    public function storeClienteRapido(Request $request)
    {
        $request->validate([
            'nome'     => 'required|string|max:150',
            'telefone' => 'required|string|max:20',
            'email'    => 'nullable|email|max:150',
            'documento'=> 'nullable|string|max:20',
        ]);

        $cliente = Cliente::create($request->only('nome', 'telefone', 'email', 'documento'));

        return response()->json($cliente);
    }
}