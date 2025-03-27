<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaExecRegistro extends Model
{
    use HasFactory;

    protected $table = 'fauna_exec_registros';
    protected $guarded = ['id'];

    public function modulo()
    {
        return $this->belongsTo(ServicoMonitoraFaunaConfigModuloAmostral::class, 'id_modulo');
    }

    public function armadilha()
    {
        return $this->belongsTo(ServicoMonitoraFaunaConfigArmadilhaMetodo::class, 'id_armadilha');
    }

    public function grupo_faunistico()
    {
        return $this->belongsTo(ServicoMonitoraFaunaExecGrupoFaunistico::class, 'id_grupo');
    }
}
