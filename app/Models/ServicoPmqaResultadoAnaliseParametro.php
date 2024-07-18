<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaResultadoAnaliseParametro extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_resultado_analise_parametros';
    protected $guarded = ['id', 'created_at'];
}