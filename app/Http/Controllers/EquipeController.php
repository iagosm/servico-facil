<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class EquipeController extends Controller
{
    public function index(): Response
    {
        $perPage = in_array(request('perPage'), [10, 20, 30, 50])
            ? (int) request('perPage')
            : 10;
 
        return Inertia::render('equipe/Index', [
            'equipe' => User::query()
                ->with('supervisor')
                ->when(request('search'), fn($q, $s) =>
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('cargo', 'like', "%{$s}%")
                )
                ->latest()
                ->paginate($perPage)
                ->withQueryString(),
            'supervisores' => User::where('ativo', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'filters' => request()->only('search', 'perPage'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'cargo'         => ['required', 'string', 'max:100'],
            'telefone'      => ['nullable', 'string', 'max:20'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'password'      => ['required', 'string', 'min:6'],
            'ativo'         => ['required', 'in:S,N'],
        ]);
        try {
            User::create([
                ...$validated,
                'password' => bcrypt($validated['password']),
                'ativo'    => $validated['ativo'] === 'S',
            ]);
            return to_route('equipe.index')
                ->with('sucesso', 'Membro cadastrado com sucesso!');
        } catch (Throwable $e) {
            return to_route('equipe.index')
                ->with('erro', 'Erro ao cadastrar membro. Tente novamente.');
        }
    }

    // public function show(User $user): Response
    // {
    //     $user->load('supervisor');
    //     return Inertia::render('equipe/Show', [
    //         'membro' => $user,
    //     ]);
    // }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', "unique:users,email,{$user->id}"],
            'cargo'         => ['required', 'string', 'max:100'],
            'telefone'      => ['nullable', 'string', 'max:20'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'password'      => ['nullable', 'string', 'min:6'],
            'ativo'         => ['required', 'in:S,N'],
        ]);
        try {
            $updateData = [
                ...$validated,
                'ativo' => $validated['ativo'] === 'S',
            ];
            if (!empty($validated['password'])) {
                $updateData['password'] = bcrypt($validated['password']);
            } else {
                unset($updateData['password']);
            }
            $user->update($updateData);
            return to_route('equipe.index')
                ->with('sucesso', 'Membro atualizado com sucesso!');
        } catch (Throwable $e) {
            return to_route('equipe.index')
                ->with('erro', 'Erro ao atualizar membro. Tente novamente.');
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $user->delete();

            return to_route('equipe.index')
                ->with('sucesso', 'Membro removido com sucesso!');

        } catch (Throwable $e) {
            return to_route('equipe.index')
                ->with('erro', 'Erro ao remover membro. Tente novamente.');
        }
    }
}