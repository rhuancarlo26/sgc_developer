<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicoPmqaCampanhaPonto extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_campanha_pontos';
    protected $guarded = ['id', 'created_at'];

    public function ponto()
    {
        return $this->belongsTo(ServicoPmqaPonto::class, 'ponto_id');
    }

    public function coleta()
    {
        return $this->hasOne(ServicoPmqaCampanhaPontoColeta::class, 'campanha_ponto_id');
    }

    public function medicao(): HasOne
    {
        return $this->hasOne(ServicoPmqaCampanhaPontoMedicao::class, 'campanha_ponto_id');
    }
}
