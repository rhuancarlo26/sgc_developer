<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SgcPmqaListaParametro extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_config_parametros';
    protected $guarded = ['id', 'created_at'];

    public function parametro(): BelongsTo
    {
        return $this->belongsTo(related: ServicoPmqaParametro::class, foreignKey: 'parametro_id');
    }

    public function parametro_lista()
    {
        return $this->belongsTo(related: SgcPmqaParametroLista::class, foreignKey: 'parametro_lista_id');
    }

    public function medicao(): BelongsTo
    {
        return $this->belongsTo(related: ServicoPmqaCampanhaPontoMedicaoParametro::class, foreignKey: 'parametro_lista_id');
    }
}
