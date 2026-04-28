<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaResultadoOutraAnalise extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_resultado_outras_analises';
    protected $guarded = ['id', 'created_at'];
    public $timestamps = false;
}
