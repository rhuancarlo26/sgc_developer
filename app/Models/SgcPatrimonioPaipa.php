<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcPatrimonioPaipa extends Model
{
    protected $table = 'sgc_patrimonio_paipa';

    protected $fillable = [
      'contrato_id',
      'subproduto',
      'empreendimento_id',
      'justificativa_sei',
      'justificativa_titulo',
      'justificativa_citacao',
      'justificativa_complementar',
      'status',
      'versao',
    ];

    protected $casts = [
        'status' => 'string',
        'versao' => 'integer'
    ];


    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    /**
     * Relacionamento com empreendimento (BelongsTo)
     * Um PAIPA pertence a um empreendimento
     */
    public function empreendimento(): BelongsTo
    {
        return $this->belongsTo(SgcvwEmpreendimentos::class, 'empreendimento_id');
    }

    /**
     * Relacionamento N:N com equipe via tabela pivot
     * Um PAIPA pode ter vários membros na equipe
     */
    public function equipe(): BelongsToMany
    {
        return $this->belongsToMany(
            SgcPatrimonioEquipe::class,
            'sgc_patrimonio_equipe_paipa',
            'paipa_id',      // FK da tabela atual na pivot
            'equipe_id'      // FK da tabela relacionada na pivot
        )->withPivot('tipo_participacao', 'data_inicio', 'data_fim', 'ativo')
         ->withTimestamps();
    }

    /**
     * Acesso direto à tabela pivot
     */
    public function vinculosEquipe(): HasMany
    {
        return $this->hasMany(SgcPatrimonioEquipePaipa::class, 'paipa_id');
    }

    // Scopes
    public function scopeRascunho($query)
    {
        return $query->where('status', 'rascunho');
    }

    public function scopeAprovado($query)
    {
        return $query->where('status', 'aprovado');
    }

    // Métodos helpers
    public function getResponsavelTecnico()
    {
        return $this->equipe()
            ->wherePivot('tipo_participacao', 'responsavel_tecnico')
            ->wherePivot('ativo', true)
            ->first();
    }

    public function getEquipeAtiva()
    {
        return $this->equipe()
            ->wherePivot('ativo', true)
            ->get();
    }
}
