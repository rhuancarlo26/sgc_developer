<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicoConOcorrSupervisaoExecOcorrenciaLevantamento extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_ocorrencia_levantamento';
    protected $guarded = ['id'];
}
