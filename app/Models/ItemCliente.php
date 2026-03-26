<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCliente extends Model
{
    // protected $fillable = ['servico_id', 'descricao'];

public function servico(): BelongsTo
{
    return $this->belongsTo(Servico::class);
}
}
