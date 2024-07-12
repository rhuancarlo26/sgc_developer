<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ServicoPmqaCampanhaPontoMedicaoParametro extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_camp_p_med_parametros';
    protected $guarded = ['id', 'created_at'];

    public function lista_parametro()
    {
        return $this->belongsTo(ServicoPmqaListaParametro::class, 'lista_parametro_id');
    }

    public function parametro()
    {
        return $this->hasOneThrough(ServicoPmqaParametro::class, ServicoPmqaListaParametro::class, 'parametro_id', 'id', 'lista_parametro_id', 'id');
    }

    public function ponto_medicao()
    {
        return $this->belongsTo(ServicoPmqaCampanhaPontoMedicao::class, 'ponto_medicao_id');
    }
}
