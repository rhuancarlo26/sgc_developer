<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupervisaoResultadoOutrasAnalises extends Model
{
    use HasFactory;

    protected $table = 'supervisao_resultado_outras_analises';
    protected $guarded = ['id'];
}
