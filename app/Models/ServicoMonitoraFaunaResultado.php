<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaResultado extends Model
{
    use HasFactory;

    protected $table = 'fauna_resultado';
    protected $guarded = ['id', 'created_at'];

    public function resultado_campanhas()
    {
        return $this->hasMany(ServicoMonitoraFaunaResultadoCampanha::class, 'id_resultado');
    }

    public function campanhas()
    {
        return $this->belongsToMany(ServicoMonitoraFaunaExecCampanha::class, 'fauna_resultado_campanha', 'id_resultado', 'id_campanha');
    }

    public function armadilha()
    {
        return $this->belongsTo(ServicoMonitoraFaunaConfigArmadilhaMetodo::class, 'id_armadilha');
    }
}
