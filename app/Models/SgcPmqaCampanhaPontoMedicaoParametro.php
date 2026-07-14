<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class SgcPmqaCampanhaPontoMedicaoParametro extends Model
{
    use HasFactory;

    protected $table = 'sgc_ponto_medicao_parametro';
    protected $guarded = ['id', 'created_at'];

    public function lista_parametro()
    {
        return $this->belongsTo(related: SgcPmqaListaParametro::class, foreignKey: 'parametro_lista_id');
    }

    public function parametro()
    {
        return $this->hasOneThrough(
            related: ServicoPmqaParametro::class,
            through: SgcPmqaListaParametro::class,
            firstKey: 'fk_parametro',
            secondKey: 'id',
            localKey: 'parametro_lista_id',
            secondLocalKey: 'id'
        );
    }

    public function ponto_medicao()
    {
        return $this->belongsTo(SgcPmqaCampanhaPontoMedicao::class, 'fk_ponto_medicao');
    }
}
