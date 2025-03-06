<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaResultadoAnaliseIqa extends Model
{
    use HasFactory;

    protected $table = 'pmqa_resultado_analise_iqa';
    protected $guarded = ['id', 'created_at'];
    public $timestamps = false;
}
