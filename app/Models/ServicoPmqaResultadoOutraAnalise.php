<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaResultadoOutraAnalise extends Model
{
    use HasFactory;

    protected $table = 'pmqa_resultado_outras_analises';
    protected $guarded = ['id', 'created_at'];
}
