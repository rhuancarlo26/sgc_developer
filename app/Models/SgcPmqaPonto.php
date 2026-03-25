<?php

namespace App\Models;

use App\Models\SgcPmqaCampanha;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SgcPmqaPonto extends Model
{
    protected $table = 'sgc_pmqa_pontos';

    protected $guarded = ['id', 'created_at'];

    public function campanha(): BelongsTo
    {
        return $this->belongsTo(SgcPmqaCampanha::class, 'campanha_id');
    }

    public function parametros(): BelongsToMany
    {
        return $this->belongsToMany(SgcPmqaParametro::class, 'sgc_pmqa_ponto_parametro', 'ponto_id', 'parametro_id')->withTimestamps();
    }
}
