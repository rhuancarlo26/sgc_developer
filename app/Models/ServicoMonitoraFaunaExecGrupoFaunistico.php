<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaExecGrupoFaunistico extends Model
{
    use HasFactory;

    protected $table = 'fauna_exec_grupo_faunistico';
    protected $guarded = ['id'];
}
