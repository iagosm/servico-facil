<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PedidoController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $pedidos = Pedido::with([
      'estoque',
      'fornecedor',
      'solicitadoPor',
      'recebidoPor',
    ])
      ->when(request('status'), fn($q, $s) => $q->where('status', $s))
      ->when(request('fornecedor_id'), fn($q, $f) => $q->where('fornecedor_id', $f))
      ->when(request('search'), fn($q, $s) => $q->where('descricao', 'like', "%{$s}%"))
      ->latest()
      ->paginate(15)
      ->withQueryString();
    return inertia('pedidos/Index', [
      'pedidos' => $pedidos,
      'resumo' => [
        'pendentes' => Pedido::where('status', 'pendente')->count(),
        'pedidos' => Pedido::where('status', 'pedido')->count(),
        'recebidos_mes' => Pedido::where('status', 'recebido')
          ->whereMonth('data_recebimento', now()->month)
          ->whereYear('data_recebimento', now()->year)
          ->count(),
      ],
      'users' => \App\Models\User::where('ativo', true)
        ->orderBy('name')
        ->get(['id', 'name', 'cargo']),
      'estoques' => \App\Models\Estoque::orderBy('nome')
        ->get(['id', 'nome', 'sku']),
      'fornecedores' => \App\Models\Fornecedor::where('ativo', true)
        ->orderBy('nome')
        ->get(['id', 'nome']),
      'filters' => request()->only(['status', 'fornecedor_id', 'search']),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $data = $request->validate([
        'descricao' => ['required', 'string'],

        'estoque_id' => ['required', 'exists:estoque,id'],
        'fornecedor_id' => ['required', 'exists:fornecedores,id'],
        'solicitado_por' => ['required', 'exists:users,id'],
        'recebido_por' => ['required', 'exists:users,id'],

        'quantidade' => ['required', 'numeric'],
        'preco_unitario' => ['required', 'numeric'],

        'status' => ['required', 'string'],

        'data_solicitacao' => ['nullable', 'date'],
        'data_pedido' => ['nullable', 'date'],
        'data_recebimento' => ['nullable', 'date'],

        'observacao' => ['nullable', 'string'],
    ]);
    Pedido::create($data);
    return to_route('pedidos.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Pedido $pedido)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Pedido $pedido)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Pedido $pedido)
  {
    //
    dd($pedido);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Pedido $pedido)
  {
    $pedido->delete();
    return to_route('pedidos.create');
  }
}
