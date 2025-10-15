<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcPmqaCampanha extends Model
{
    protected $table = 'sgc_pmqa_campanhas';

    protected $fillable = [
        'id_contrato',
        'subproduto',
        'fase',
        'status',
        'id_campanha',
        'observacoes',
        'created_at',
        'update_at'
    ];

    public function pontos(): HasMany
    {
        return $this->hasMany(SgcPmqaPonto::class, 'camapanha_id');
    }

    public function parametros(): HasMany
    {
        return $this->hasMany(SgcPmqaParametro::class, 'campanha_id');
    }
}
