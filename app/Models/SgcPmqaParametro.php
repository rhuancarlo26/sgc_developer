<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SgcPmqaParametro extends Model
{
    protected $table = 'sgc_pmqa_parametros_lista';

    protected $fillable = [
        'campanha_id',
        'nome', // Em vez de 'nome_parametro'
        'chave', // Adicionado, pois existe no banco
        'medir_iqa', // Adicionado, pois existe no banco
    ];

    public function campanha(): BelongsTo
    {
        return $this->belongsTo(SgcPmqaCampanha::class, 'campanha_id');
    }

    public function parametros(): BelongsToMany
    {
        return $this->belongsToMany(
            related: SgcPmqaParametro::class,
            table: 'sgc_pmqa_parametro_pivot',
            foreignPivotKey: 'lista_id',
            relatedPivotKey: 'parametro_id'
        )->withPivot('observacoes');
    }
}
