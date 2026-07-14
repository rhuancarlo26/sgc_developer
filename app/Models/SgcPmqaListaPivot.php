<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SgcPmqaListaPivot extends Model
{
    protected $table = 'sgc_pmqa_parametro_pivot';

    protected $fillable = [
        'lista_id',
        'parametro_id',
        'observacoes',
    ];

    public function lista(): BelongsToMany
    {
        return $this->belongsToMany(
            related: SgcPmqaParametro::class,
            table: 'sgc_pmqa_parametro_pivot',
            foreignPivotKey: 'parametro_id',
            relatedPivotKey: 'lista_id'
        )->withPivot('observacoes');
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
