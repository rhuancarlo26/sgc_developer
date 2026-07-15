<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcPatrimonioEquipe extends Model
{
    protected $table = 'sgc_patrimonio_equipe';

    protected $fillable = [
        'nome', 'cnpj', 'cpf', 'email', 'profissao',
        'carteira_profissional', 'obs', 'numero_registro',
        'conselho_classe', 'ct', 'funcao', 'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento N:N com PAIPA via tabela pivot
     * Um membro da equipe pode participar de vários PAIPAs
     */
    public function paipas(): BelongsToMany
    {
        return $this->belongsToMany(
            SgcPatrimonioPaipa::class,
            'sgc_patrimonio_equipe_paipa',
            'equipe_id',     // FK da tabela atual na pivot
            'paipa_id'       // FK da tabela relacionada na pivot
        )->withPivot('tipo_participacao', 'data_inicio', 'data_fim', 'ativo')
         ->withTimestamps();
    }

    /**
     * Acesso direto à tabela pivot
     */
    public function vinculos(): HasMany
    {
        return $this->hasMany(SgcPatrimonioEquipePaipa::class, 'equipe_id');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorProfissao($query, $profissao)
    {
        return $query->where('profissao', 'LIKE', "%{$profissao}%");
    }
}
