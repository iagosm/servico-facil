<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoProblema extends Model
{
  protected $fillable = [
    'servico_equipamento_id',
    'descricao', 'laudo_tecnico', 'resolvido',
  ];

public function equipamento(): BelongsTo
{
    return $this->belongsTo(ServicoEquipamento::class, 'servico_equipamento_id');
}
}
