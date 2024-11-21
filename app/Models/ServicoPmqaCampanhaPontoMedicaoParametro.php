<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ServicoPmqaCampanhaPontoMedicaoParametro extends Model
{
    use HasFactory;

    protected $table = 'exec_ponto_medicao_parametro';
    protected $guarded = ['id', 'created_at'];

    public function lista_parametro()
    {
        return $this->belongsTo(related: ServicoPmqaListaParametro::class, foreignKey: 'lista_parametro_id');
    }

    public function parametro()
    {
        return $this->hasOneThrough(
            related: ServicoPmqaParametro::class,
            through: ServicoPmqaListaParametro::class,
            firstKey: 'fk_parametro',
            secondKey: 'id',
            localKey: 'fk_parametro_lista',
            secondLocalKey: 'id'
        );
    }

    public function ponto_medicao()
    {
        return $this->belongsTo(ServicoPmqaCampanhaPontoMedicao::class, 'fk_ponto_medicao');
    }
}
