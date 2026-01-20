<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcPmqaParametroLista extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_parametros_lista';
    protected $guarded = ['id', 'created_at'];
    protected $appends = ['lista_parametros'];
    protected $casts = ['medir_iqa' => 'bool'];

    public function parametros(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ServicoPmqaParametro::class,
            table: 'sgc_pmqa_config_parametros',
            foreignPivotKey: 'parametro_lista_id',
            relatedPivotKey: 'parametro_id'
        );
    }

    public function parametros_vinculados(): HasMany
    {
        return $this->hasMany(related: SgcPmqaListaParametro::class, foreignKey: 'parametro_lista_id');
    }

    public function pontos(): BelongsToMany
    {
        return $this->belongsToMany(ServicoPmqaPonto::class, 'sgc_pmqa_config_parametros', 'parametro_lista_id', 'fk_ponto');
    }

    public function listaParametros(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parametros = [];

                foreach ($this->parametros as $value) {
                    $uf = $value->nome;

                    $uf ? array_push($parametros, trim($uf)) : '';
                }

                return implode(",", $parametros);
            }
        );
    }
}
