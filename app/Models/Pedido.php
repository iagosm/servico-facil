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

protected $fillable = [
        'estoque_id',
        'fornecedor_id',
        'solicitado_por',
        'recebido_por',
        'descricao',
        'quantidade',
        'preco_unitario',
        'status',
        'data_solicitacao',
        'data_pedido',
        'data_recebimento',
        'observacao',
    ];

    protected $casts = [
        'data_solicitacao' => 'date',
        'data_pedido'      => 'date',
        'data_recebimento' => 'date',
        'preco_unitario'   => 'decimal:2',
    ];

    public function estoque()
    {
        return $this->belongsTo(Estoque::class);
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function solicitadoPor()
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function recebidoPor()
    {
        return $this->belongsTo(User::class, 'recebido_por');
    }
}
