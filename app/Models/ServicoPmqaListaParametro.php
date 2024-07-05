<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicoPmqaListaParametro extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_lista_parametros';
    protected $guarded = ['id', 'created_at'];

    public function parametro(): BelongsTo
    {
        return $this->belongsTo(ServicoPmqaParametro::class, 'parametro_id');
    }

    public function medicao(): HasOne
    {
        return $this->hasOne(ServicoPmqaCampanhaPontoMedicaoParametro::class, 'lista_parametro_id');
    }
}