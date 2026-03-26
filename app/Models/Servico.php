<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servico extends Model
{
    // protected $fillable = [
    //       'numero', 'cliente_id', 'supervisor_id',
    //       'tipo', 'status', 'prioridade',
    //       'obs_internas', 'obs_cliente',
    //       'valor_cobrado', 'custo_total',
    //       'data_entrada', 'data_previsao',
    //       'data_conclusao', 'data_entrega',
    //       'validade_orcamento',
    // ];

    protected $casts = [
        'data_entrada'        => 'date',
        'data_previsao'       => 'date',
        'data_conclusao'      => 'date',
        'data_entrega'        => 'date',
        'validade_orcamento'  => 'date',
        'valor_cobrado'       => 'decimal:2',
    'custo_total'         => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function equipamentos(): HasMany
    {
        return $this->hasMany(ServicoEquipamento::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(ServicoUser::class);
    }

    public function historico(): HasMany
    {
        return $this->hasMany(ServicoStatus::class)->orderBy('created_at');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemCliente::class);
    }
}
