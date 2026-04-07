<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome', 'telefone', 'email', 'endereco', 'cidade', 'estado', 'cep', 'documento', 'observacoes'])]
class Cliente extends Model
{
    public function servicos(): HasMany
    {
        return $this->hasMany(Servico::class);
    }
}
