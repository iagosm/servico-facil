<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
      $users = User::with('supervisor')
        ->whereNotNull('cargo')
        ->orderBy('name')
        ->get();
      return inertia('Users/Index', ['users' => $users]);
    }
}
