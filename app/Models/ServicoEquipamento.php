<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoEquipamento extends Model
{
  //  protected $fillable = [
  //   'servico_id', 'tipo', 'marca',
  //   'modelo', 'numero_serie', 'condicao_entrada',
  // ];

  public function servico(): BelongsTo
  {
      return $this->belongsTo(Servico::class);
  }

  public function problemas(): HasMany
  {
      return $this->hasMany(ServicoProblema::class);
  }

  public function pecas(): HasMany
  {
      return $this->hasMany(ServicoPeca::class);
  }
}
