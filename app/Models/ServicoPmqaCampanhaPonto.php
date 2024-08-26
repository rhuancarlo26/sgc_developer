<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicoPmqaCampanhaPonto extends Model
{
    use HasFactory;

    protected $table   = 'exec_campanha_ponto';
    protected $guarded = ['id', 'created_at'];

    public function ponto()
    {
        return $this->belongsTo(related: ServicoPmqaPonto::class, foreignKey: 'fk_ponto');
    }

    public function coleta()
    {
        return $this->hasOne(related: ServicoPmqaCampanhaPontoColeta::class, foreignKey: 'fk_campanha_ponto');
    }

    public function medicao(): HasOne
    {
        return $this->hasOne(related: ServicoPmqaCampanhaPontoMedicao::class, foreignKey: 'fk_campanha_ponto');
    }
}
