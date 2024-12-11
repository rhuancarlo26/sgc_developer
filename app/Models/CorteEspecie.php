<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorteEspecie extends Model
{
    use SoftDeletes;

    protected $table = 'corte_especie';

    protected $guarded = ['id', 'created_at'];

}
