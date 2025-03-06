<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupervicaoExecOcorrenciaRegistro extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_ocorrencia_registro_fotografico';
    protected $guarded = ['id'];
}