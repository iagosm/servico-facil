<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servico extends Model
{
    protected $fillable = [
        'numero',
        'cliente_id',
        'supervisor_id',
        'tipo',
        'status',
        'prioridade',
        'obs_internas',
        'obs_cliente',
        'valor_cobrado',
        'custo_total',
        'data_entrada',
        'data_previsao',
        'data_conclusao',
        'data_entrega',
        'validade_orcamento',
    ];

    protected $casts = [
        'data_entrada'       => 'datetime',
        'data_previsao'      => 'datetime',
        'data_conclusao'     => 'datetime',
        'data_entrega'       => 'datetime',
        'validade_orcamento' => 'datetime',
        'valor_cobrado'      => 'decimal:2',
        'custo_total'        => 'decimal:2',
    ];

    // ─── Relacionamentos ──────────────────────────────────────────────

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

    /**
     * Itens físicos deixados pelo cliente (carregador, capa, etc.)
     * Tabela: itens_cliente
     */
    public function itensCliente(): HasMany
    {
        return $this->hasMany(ItemCliente::class);
    }

    /**
     * Técnicos vinculados à OS (pivot: servico_users)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'servico_users')
                    ->withPivot(['papel', 'data_inicio', 'data_fim', 'observacao'])
                    ->withTimestamps();
    }

    /**
     * Timeline de mudanças de status
     * Tabela: servico_status
     */
    public function statusTimeline(): HasMany
    {
        return $this->hasMany(ServicoStatus::class)->with('user')->orderBy('created_at');
    }
}