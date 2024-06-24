<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaParametroLista extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_parametro_listas';
    protected $guarded = ['id', 'created_at'];
    protected $appends = ['lista_parametros'];
    protected $casts = [
        'medir_iqa' => 'bool'
    ];

    public function parametros()
    {
        return $this->belongsToMany(ServicoPmqaParametro::class, 'servico_pmqa_lista_parametros', 'lista_parametro_id', 'parametro_id');
    }

    public function pontos()
    {
        return $this->belongsToMany(ServicoPmqaPonto::class, 'servico_pmqa_lista_pontos', 'lista_parametro_id', 'ponto_id');
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