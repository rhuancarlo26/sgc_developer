<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SgcPmqaPonto extends Model
{
    protected $table = 'sgc_pmqa_pontos';

    protected $guarded = ['id', 'created_at'];

    /** 🔗 PMQA (raiz do domínio) */
    public function pmqa(): BelongsTo
    {
        return $this->belongsTo(SgcPmqa::class, 'pmqa_id');
    }

    public function vinculacao()
    {
        return $this->hasOne(SgcPmqaListaPonto::class, 'ponto_id');
    }

    public function lista()
    {
        return $this->hasOneThrough(
            SgcPmqaParametroLista::class,
            SgcPmqaListaPonto::class,
            'ponto_id', // FK no pivot
            'id',       // PK da lista
            'id',       // PK do ponto
            'lista_id'  // FK para lista no pivot
        );
    }
}
