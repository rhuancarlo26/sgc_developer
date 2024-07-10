<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaResultadoAnaliseIqa extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_resultado_analise_iqas';
    protected $guarded = ['id', 'created_at'];
}