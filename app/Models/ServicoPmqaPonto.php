<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaPonto extends Model
{
    use HasFactory;

    protected $table = 'config_pontos';
    protected $guarded = ['id', 'created_at'];

    public function vinculado()
    {
        return $this->hasOne(ServicoPmqaListaPonto::class, 'fk_ponto');
    }

    public function campanhas()
    {
        return $this->belongsToMany(ServicoPmqaCampanha::class, 'servico_pmqa_campanha_pontos', 'ponto_id', 'campanha_id');
    }

    public function lista()
    {
        return $this->hasOneThrough(ServicoPmqaParametroLista::class, ServicoPmqaListaPonto::class, 'fk_ponto', 'id', 'id', 'lista_parametro_id');
    }
}
