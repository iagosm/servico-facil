<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoUser extends Model
{
//     protected $fillable = [
//     'servico_id', 'user_id', 'papel',
//     'data_inicio', 'data_fim', 'observacao',
// ];

protected $casts = [
    'data_inicio' => 'datetime',
    'data_fim'    => 'datetime',
];

public function servico(): BelongsTo
{
    return $this->belongsTo(Servico::class);
}

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

}
