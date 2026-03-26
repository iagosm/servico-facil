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
      ]);//


    }
}
