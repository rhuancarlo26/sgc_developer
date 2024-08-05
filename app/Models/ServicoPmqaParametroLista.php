<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoPmqaParametroLista extends Model
{
    use HasFactory;

    protected $table = 'config_parametros_lista';
    protected $guarded = ['id', 'created_at'];
    protected $appends = ['lista_parametros'];
    protected $casts = [
        'medir_iqa' => 'bool'
    ];

    public function parametros(): BelongsToMany
    {
        return $this->belongsToMany(ServicoPmqaParametro::class, 'config_parametros', 'fk_parametro_lista', 'fk_parametro');
    }

    public function parametros_vinculados(): HasMany
    {
        return $this->hasMany(ServicoPmqaListaParametro::class, 'lista_parametro_id');
    }

    public function pontos(): BelongsToMany
    {
        return $this->belongsToMany(ServicoPmqaPonto::class, 'config_vinculacao', 'fk_lista', 'fk_ponto');
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
