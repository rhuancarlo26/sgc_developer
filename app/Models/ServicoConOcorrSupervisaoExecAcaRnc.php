<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupervisaoExecAcaRnc extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_aca_rnc';
    protected $guarded = ['id'];
}
