<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(): Response
    {
        $perPage = in_array(request('perPage'), [10, 20, 30, 50]) 
        ? (int) request('perPage') 
        : 10;
        return Inertia::render('clientes/Index', [
            'clientes' => Cliente::query()
            ->when(request('search'), fn($q, $s) =>
              $q->where('nome', 'like', "%{$s}%")
              ->orWhere('email', 'like', "%{$s}%")
              ->orWhere('telefone', 'like', "%{$s}%")
            )
              ->latest()
              ->paginate($perPage)
              ->withQueryString(),
            'filters' => request()->only('search', 'perPage'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('clientes/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'telefone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'cep' => ['nullable', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:20'],
            'observacoes' => ['nullable', 'string'],
        ]);
        try {
          Cliente::query()->create($validated);
           return to_route('clientes.index')->with('sucesso', 'Cliente cadastrado com sucesso!');
        } catch (\Throwable $th) {
           return to_route('clientes.index')->with('erro', 'Erro ao cadastrar cliente. Tente novamente.');
        }
    }

    public function edit(Cliente $cliente): Response
    {
        return Inertia::render('clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'telefone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'cep' => ['nullable', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:20'],
            'observacoes' => ['nullable', 'string'],
        ]);
        try {
          $cliente->update($validated);
          return to_route('clientes.index')->with('sucesso', 'Cliente atualizado com sucesso!');
        } catch (\Throwable $th) {
           return to_route('clientes.index')->with('erro', 'Erro ao atualizado cliente. Tente novamente.');
        }
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
      try {
        
        $cliente->delete();
         return to_route('clientes.index')
                ->with('sucesso', 'Cliente removido com sucesso!');
      } catch (\Throwable $th) {
        return to_route('clientes.index')
                ->with('erro', 'Erro ao remover cliente. Tente novamente.');
      }
    }
}
