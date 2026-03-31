<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FornecedorController extends Controller
{
    public function index()
    {
        return Inertia::render('fornecedores/Index', [
          'fornecedores' => Fornecedor::query()->latest()->paginate(10)
        ]);
    }

    // public function create()
    // {
    //     //
    // }

    public function store(Request $request)
    {
        $request->validate([
            'nome'             => 'required|string|max:120',
            'telefone'              => 'nullable|string|max:20|',
            'email'        => 'nullable|string',
            'contato'       => 'required|string|min:0',
            'site'=> 'required|string',
            'observacoes'      => 'required|string',
            'ativo'      => 'required|string',
        ]);
        Fornecedor::query()->create($request->all());
        return to_route('fornecedores.index');
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
            'nome'             => 'required|string|max:120',
            'telefone'    => 'nullable|string|max:20',
            'email'        => 'required|string',
            'contato'       => 'required|string',
            'site'=> 'required|string',
            'observacoes'      => 'required|string',
            'ativo'      => 'required|string',
        ]);
        $fornecedor->update($validated);
        return to_route('fornecedores.index');
    }

    public function destroy(Fornecedor $fornecedor)
    {
        $fornecedor->delete();
        return to_route('fornecedores.index');
    }
}
