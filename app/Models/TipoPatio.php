<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoPatio extends Model
{
    use SoftDeletes;

    protected $table = 'tipo_patio';
}
