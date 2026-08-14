<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SgcPatrimonioEquipePaipa extends Model
{
    protected $table = 'sgc_patrimonio_equipe_paipa';

    protected $fillable = [
        'equipe_id', 'paipa_id', 'tipo_participacao',
        'data_inicio', 'data_fim', 'ativo'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento com a equipe
     */
    public function equipe(): BelongsTo
    {
        return $this->belongsTo(SgcPatrimonioEquipe::class, 'equipe_id');
    }

    /**
     * Relacionamento com o PAIPA
     */
    public function paipa(): BelongsTo
    {
        return $this->belongsTo(SgcPatrimonioPaipa::class, 'paipa_id');
    }

    // Accessors
    public function getTipoParticipacaoLabelAttribute(): string
    {
        $labels = [
            'responsavel_tecnico' => 'Responsável Técnico',
            'coordenador' => 'Coordenador',
            'assistente' => 'Assistente',
            'consultor' => 'Consultor',
            'fiscal' => 'Fiscal'
        ];

        return $labels[$this->tipo_participacao] ?? $this->tipo_participacao;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    // Method para encerrar participação
    public function encerrar(): self
    {
        $this->ativo = false;
        $this->data_fim = now();
        $this->save();

        return $this;
    }
}
