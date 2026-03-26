<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoStatus extends Model
{
//     protected $fillable = [
//     'servico_id', 'user_id',
//     'status_anterior', 'status_novo', 'observacao',
// ];

  public function servico(): BelongsTo
  {
      return $this->belongsTo(Servico::class);
  }

  public function user(): BelongsTo
  {
      return $this->belongsTo(User::class);
  }
}
