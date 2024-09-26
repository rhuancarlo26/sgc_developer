<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaResultadoOutrasAnalise extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_resultado_outras_analises';

    protected $guarded = ['id'];
}
