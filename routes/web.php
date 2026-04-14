<?php

use App\Http\Controllers\EquipeController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClienteController;

  
Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('clientes', ClienteController::class)->except(['show']);
    Route::resource('estoque', EstoqueController::class)->except(['show']);
    Route::resource('pedidos', PedidoController::class)->except(['show']);
    Route::resource('equipe', EquipeController::class)->except(['show'])->parameters(['equipe' => 'user']);
    Route::resource('fornecedores', FornecedorController::class)->except(['show'])->parameters(['fornecedores' => 'fornecedor']);
    Route::inertia('teste-pacotes', 'TestePacotes')->name('teste-pacotes');
});

require __DIR__.'/settings.php';