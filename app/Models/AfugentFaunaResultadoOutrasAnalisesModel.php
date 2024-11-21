<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AfugentFaunaResultadoOutrasAnalisesModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_resultado_outras_analises';

    protected $guarded = ['id', 'created_at'];
}
