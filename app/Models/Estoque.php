<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estoque extends Model
{
//     protected $fillable = [
//     'nome', 'descricao', 'sku',
//     'quantidade', 'quantidade_minima',
//     'preco_custo', 'preco_venda',
// ];

  protected $casts = [
      'preco_custo' => 'decimal:2',
      'preco_venda' => 'decimal:2',
  ];

  public function pecas(): HasMany
  {
      return $this->hasMany(ServicoPeca::class);
  }

  public function pedidos(): HasMany
  {
      return $this->hasMany(Pedido::class);
  }

  public function abaixoDoMinimo(): bool
  {
      return $this->quantidade < $this->quantidade_minima;
  }
}
