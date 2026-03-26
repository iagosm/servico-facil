<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoPeca extends Model
{
//     protected $fillable = [
//     'servico_equipamento_id', 'estoque_id',
//     'descricao', 'quantidade',
//     'preco_custo', 'preco_cobrado',
// ];

  protected $casts = [
      'preco_custo'    => 'decimal:2',
      'preco_cobrado'  => 'decimal:2',
  ];

  public function equipamento(): BelongsTo
  {
      return $this->belongsTo(ServicoEquipamento::class, 'servico_equipamento_id');
  }

  public function estoque(): BelongsTo
  {
      return $this->belongsTo(Estoque::class);
  }
}
