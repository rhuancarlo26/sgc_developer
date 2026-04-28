<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaResultadoAnaliseParametro extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_resultado_analises_parametros';
    protected $guarded = ['id', 'created_at'];
    public $timestamps = false;
}
