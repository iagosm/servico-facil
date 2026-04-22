<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ServicoController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClienteController;

  
Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('financeiro', [FinanceiroController::class, 'index'])->name('financeiro');
    Route::resource('clientes', ClienteController::class)->except(['show']);
    Route::resource('estoque', EstoqueController::class)->except(['show']);
    Route::resource('pedidos', PedidoController::class)->except(['show']);
    Route::resource('servicos', ServicoController::class);
 
Route::patch('servicos/{servico}/status',
    [ServicoController::class, 'updateStatus'])->name('servicos.updateStatus');
 
// ↓ NOVA rota — criação rápida de cliente via fetch (chamada pelo ClienteQuickCreate.vue)
Route::post('servicos/cliente-rapido',
    [ServicoController::class, 'storeClienteRapido'])->name('servicos.clienteRapido');
 
// ⚠️ IMPORTANTE: a rota 'cliente-rapido' deve ficar ANTES de Route::resource('servicos', ...)
// pois o resource registra GET/POST /servicos/{servico} e pode conflitar.
// Coloque nesta ordem:
 
Route::post('servicos/cliente-rapido',
    [ServicoController::class, 'storeClienteRapido'])->name('servicos.clienteRapido');
 
Route::resource('servicos', ServicoController::class);
 
Route::patch('servicos/{servico}/status',
    [ServicoController::class, 'updateStatus'])->name('servicos.updateStatus');
    Route::resource('equipe', EquipeController::class)->except(['show'])->parameters(['equipe' => 'user']);
    Route::resource('fornecedores', FornecedorController::class)->except(['show'])->parameters(['fornecedores' => 'fornecedor']);
    Route::inertia('teste-pacotes', 'TestePacotes')->name('teste-pacotes');
});

require __DIR__.'/settings.php';