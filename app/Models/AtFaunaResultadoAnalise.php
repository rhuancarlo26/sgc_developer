<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaResultadoAnalise extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_resultado_analise';

    protected $guarded = ['id'];
}
