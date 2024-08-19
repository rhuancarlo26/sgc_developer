<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Hasone;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServicoPmqaResultado extends Model
{
    use HasFactory;

    protected $table = 'pmqa_resultado';
    protected $guarded = ['id', 'created_at'];

    public function campanhas(): BelongsToMany
    {
        return $this->belongsToMany(related: ServicoPmqaCampanha::class, table: 'pmqa_resultado_campanhas', foreignPivotKey: 'fk_resultado', relatedPivotKey: 'fk_campanha');
    }

    public function analises(): HasMany
    {
        return $this->hasMany(related: ServicoPmqaResultadoAnaliseParametro::class, foreignKey: 'fk_resultado');
    }

    public function analise_iqa(): Hasone
    {
        return $this->hasOne(related: ServicoPmqaResultadoAnaliseIqa::class, foreignKey: 'fk_resultado');
    }

    public function outras_analises(): HasMany
    {
        return $this->hasMany(related: ServicoPmqaResultadoOutraAnalise::class, foreignKey: 'fk_resultado');
    }
}
