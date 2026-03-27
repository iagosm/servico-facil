<?php

namespace App\Http\Controllers;

use App\Models\Estoque;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstoqueController extends Controller
{
    public function index()
    {
      return Inertia::render('estoque/Index', [
        'estoque' => Estoque::query()->latest()
                ->paginate(10)
      ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nome'             => 'required|string|max:120',
            'sku'              => 'nullable|string|max:60|unique:estoque,sku',
            'descricao'        => 'nullable|string',
            'quantidade'       => 'required|integer|min:0',
            'quantidade_minima'=> 'required|integer|min:0',
            'preco_custo'      => 'required|numeric|min:0',
            'preco_venda'      => 'required|numeric|min:0',
        ]);
        Estoque::query()->create($request->all());
        return to_route('estoque.index');
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
        $estoque->update($validated);
        return to_route('estoque.index');
    }

    public function destroy(Estoque $estoque)
    {
      $estoque->delete();
      return to_route('estoque.index');
    }
}
