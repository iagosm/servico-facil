<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstoqueController extends Controller
{
    public function index()
    {
      $tipo = request('tipo', 'insumo');
      return inertia('estoque/Index', [
          'estoque' => Estoque::query()
              ->where('tipo', $tipo)
              ->when(request('search'), fn($q, $s) =>
                  $q->where('nome', 'like', "%{$s}%")
                    ->orWhere('sku',  'like', "%{$s}%"))
              ->paginate(request('perPage', 10))
              ->withQueryString(),
          'filters' => request()->only('search', 'perPage', 'tipo'),
      ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo'              => 'required|in:insumo,venda',
            'condicao' => 'required|in:novo,conservado,com_defeito,para_pecas',
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
            'tipo'              => 'required|in:insumo,venda',
            'condicao' => 'required|in:novo,conservado,com_defeito,para_pecas',
            'nome'             => 'required|string|max:120',
            'sku'               => 'nullable|string|max:60|unique:estoque,sku,' . $estoque->id,
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
