<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends Model
{
//     protected $fillable = [
//     'estoque_id', 'descricao',
//     'quantidade', 'status', 'observacao',
// ];

public function estoque(): BelongsTo
{
    return $this->belongsTo(Estoque::class);
}
}
