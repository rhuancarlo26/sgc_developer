<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicoPmqaListaParametro extends Model
{
    use HasFactory;

    protected $table = 'config_parametros';
    protected $guarded = ['id', 'created_at'];

    public function parametro(): BelongsTo
    {
        return $this->belongsTo(related: ServicoPmqaParametro::class, foreignKey: 'fk_parametro');
    }

    public function parametro_lista()
    {
        return $this->belongsTo(related: ServicoPmqaParametroLista::class, foreignKey: 'fk_parametro_lista');
    }

    public function medicao(): BelongsTo
    {
        return $this->belongsTo(related: ServicoPmqaCampanhaPontoMedicaoParametro::class, foreignKey: 'fk_parametro_lista');
    }
}
