<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaResultadoCampanha extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_resultado_campanhas';
    protected $guarded = ['id', 'created_at'];
}