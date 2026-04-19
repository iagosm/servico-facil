<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstoqueController extends Controller
{
    public function index()
    {
      $perPage = in_array(request('perPage'), [10, 20, 30, 50])
            ? (int) request('perPage')
            : 10;
      return Inertia::render('estoque/Index', [
        'estoque' => Estoque::query()
        ->when(request('search'), fn($q, $s) => 
          $q->where('nome', 'like', "%{$s}%")
            ->orWhere('sku', 'like', "%{$s}%")
        )
        ->latest()
        ->paginate($perPage)
        ->withQueryString(),
        'filters' => request()->only('search', 'perPage'),
      ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'             => 'required|string|max:120',
            'sku'              => 'nullable|string|max:60|unique:estoque,sku',
            'descricao'        => 'nullable|string',
            'quantidade'       => 'required|integer|min:0',
            'quantidade_minima'=> 'required|integer|min:0',
            'preco_custo'      => 'required|numeric|min:0',
            'preco_venda'      => 'required|numeric|min:0',
        ]);
        try {
          Estoque::query()->create($request->all());
          return to_route('estoque.index')->with('sucesso', 'Item criado com sucesso!');
        } catch (\Throwable $th) {
          return to_route('estoque.index')->with('erro', 'Erro ao criar item. Tente novamente');
        }
    }

    public function update(Request $request, Estoque $estoque)
    {
        $validated = $request->validate([
            'nome'             => 'required|string|max:120',
            'sku'              => 'nullable|string|max:60|unique:estoque,sku',
            'descricao'        => 'nullable|string',
            'quantidade'       => 'required|integer|min:0',
            'quantidade_minima'=> 'required|integer|min:0',
            'preco_custo'      => 'required|numeric|min:0',
            'preco_venda'      => 'required|numeric|min:0',
        ]);
        try {
          $estoque->update($validated);
          return to_route('estoque.index')->with('sucesso', 'Item atualizado com sucesso.');
        } catch (\Throwable $th) {
          return to_route('estoque.index')->with('erro', 'Erro ao atualizar item. Tente novamente');
        }
    }

    public function destroy(Estoque $estoque)
    {
      try {
        $estoque->delete();
        return to_route('estoque.index')->with('sucesso', 'Item removido com sucesso');
      } catch (\Throwable $th) {
        return to_route('estoque.index')->with('erro', 'Erro ao remover item. Tente novamente');
      }
      $estoque->delete();
      return to_route('estoque.index');
    }
}
