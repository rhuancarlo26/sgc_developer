<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanha extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_campanhas';
    protected $guarded = ['id', 'created_at'];

    public function pontos()
    {
        return $this->belongsToMany(ServicoPmqaPonto::class, 'servico_pmqa_campanha_pontos', 'campanha_id', 'ponto_id');
    }

    public function medicoes()
    {
        return $this->hasMany(ServicoPmqaCampanhaPontoMedicaoParametro::class, 'campanha_id');
    }
}