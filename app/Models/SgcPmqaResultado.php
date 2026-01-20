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

    protected $table = 'sgc_pmqa_resultado';
    protected $guarded = ['id', 'created_at'];

    public function campanhas(): BelongsToMany
    {
        return $this->belongsToMany(SgcPmqaCampanha::class, 'sgc_pmqa_resultado_campanhas', 'fk_resultado', 'fk_pmqa_campanha');
    }

    public function analises(): HasMany
    {
        return $this->hasMany(ServicoPmqaResultadoAnaliseParametro::class, 'fk_resultado');
    }

    public function analise_iqa(): Hasone
    {
        return $this->hasOne(ServicoPmqaResultadoAnaliseIqa::class, 'fk_resultado');
    }

    public function outras_analises(): HasMany
    {
        return $this->hasMany(ServicoPmqaResultadoOutraAnalise::class, 'fk_resultado');
    }
}
