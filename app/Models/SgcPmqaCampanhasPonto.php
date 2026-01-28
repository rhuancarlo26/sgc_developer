<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SgcPmqaCampanhasPonto extends Model
{
    use HasFactory;

    protected $table   = 'sgc_pmqa_campanhas_pontos';
    protected $guarded = ['id', 'created_at'];

    public function ponto()
    {
        return $this->belongsTo(related: SgcPmqaPonto::class, foreignKey: 'ponto_id');
    }

    public function coleta()
    {
        return $this->hasOne(related: SgcPmqaCampanhaPontoColeta::class, foreignKey: 'campanha_ponto_id');
    }

    public function medicao(): HasOne
    {
        return $this->hasOne(related: SgcPmqaCampanhaPontoMedicao::class, foreignKey: 'campanha_ponto_id');
    }
}
