<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaExecCampanhaProfissGrupo extends Model
{
    use HasFactory;

    protected $table = 'fauna_exec_campanhas_profiss_grupo';
    protected $guarded = ['id'];

    public function grupo_faunistico()
    {
        return $this->belongsTo(ServicoMonitoraFaunaExecGrupoFaunistico::class, 'id_grupo_faunistico');
    }

    public function rh()
    {
        return $this->belongsTo(ServicoRh::class, 'id_profissional');
    }
}
