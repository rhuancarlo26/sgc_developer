<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Hasone;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SgcPmqaResultado extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_resultados';
    protected $guarded = ['id', 'created_at'];

    public function campanhas(): BelongsToMany
    {
        return $this->belongsToMany(SgcPmqaCampanha::class, 'sgc_pmqa_resultado_campanhas', 'sgc_resultado_id', 'campanha_id');
    }

    public function analises(): HasMany
    {
        return $this->hasMany(SgcPmqaResultadoAnaliseParametro::class, 'sgc_resultado_id');
    }

    public function analise_iqa(): Hasone
    {
        return $this->hasOne(SgcPmqaResultadoAnaliseIqa::class, 'sgc_resultado_id');
    }

    public function outras_analises(): HasMany
    {
        return $this->hasMany(SgcPmqaResultadoOutraAnalise::class, 'sgc_resultado_id');
    }
}
