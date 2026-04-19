<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FornecedorController extends Controller
{
    public function index()
    {
      $perPage = in_array(request('perPage'), [10, 20, 30, 50])
            ? (int) request('perPage')
            : 10;
        return Inertia::render('fornecedores/Index', [
            'fornecedores' => Fornecedor::query()
                ->when(request('search'), fn($q, $s) => 
                $q->where('nome', 'like', "%{$s}%")
                    ->orWhere('telefone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('contato', 'like', "%{$s}%")
                    ->orWhere('site', 'like', "%{$s}%")
                )
                ->latest()->paginate($perPage)->withQueryString(),
                'filters' => request()->only('search', 'perPage'),
        ]);
    }

    // public function create()
    // {
    //     //
    // }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:120',
            'telefone' => 'nullable|string|max:20|',
            'email' => 'nullable|string',
            'contato' => 'required|string|min:0',
            'site' => 'required|string',
            'observacoes' => 'required|string',
            'ativo' => 'required|string',
        ]);
        try {
            Fornecedor::query()->create($request->all());

            return to_route('fornecedores.index')->with('sucesso', 'Fornecedor criado com sucesso!');
        } catch (\Throwable $th) {
            return to_route('fornecedores.index')->with('erro', 'Erro ao criar fornecedor criado com sucesso!');
        }
    }

    // public function show(Fornecedor $fornecedor)
    // {
    //     //
    // }

    // public function edit(Fornecedor $fornecedor)
    // {
    //     dd('test1', $fornecedor);
    // }

    public function update(Request $request, Fornecedor $fornecedor)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:120',
            'telefone' => 'nullable|string|max:20',
            'email' => 'required|string',
            'contato' => 'required|string',
            'site' => 'required|string',
            'observacoes' => 'required|string',
            'ativo' => 'required|string',
        ]);
        try {
            $fornecedor->update($validated);

            return to_route('fornecedores.index')->with('sucesso', 'Fornecedor atualizado com sucesso!');
        } catch (\Throwable $th) {
            return to_route('fornecedores.index')->with('erro', 'Erro ao atualizar o fornecedor. Tente novamente');
        }
    }

    public function destroy(Fornecedor $fornecedor)
    {
        try {
            $fornecedor->delete();

            return to_route('fornecedores.index')->with('sucesso', 'Fornecedor removido com sucesso');
        } catch (\Throwable $th) {
            return to_route('fornecedores.index')->with('erro', 'Erro ao remover fornecedor. Tente novamente');
        }
    }
}
