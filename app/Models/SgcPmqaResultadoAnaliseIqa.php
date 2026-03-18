<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaResultadoAnaliseIqa extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_resultado_analise_iqa';
    protected $guarded = ['id', 'created_at'];
    public $timestamps = false;
}
