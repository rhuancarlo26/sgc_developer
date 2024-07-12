<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanhaPontoMedicaoParametro extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_camp_p_med_parametros';
    protected $guarded = ['id', 'created_at'];

    public function lista_parametro()
    {
        return $this->belongsTo(ServicoPmqaListaParametro::class, 'lista_parametro_id');
    }
}
