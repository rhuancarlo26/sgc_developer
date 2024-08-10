<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupervisaoExecOcorrenciaVistoria extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_ocorrencia_vistoria';
    protected $guarded = ['id'];
}
