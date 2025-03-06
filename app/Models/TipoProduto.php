<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoProduto extends Model
{
    use SoftDeletes;

    protected $table = 'tipo_produto';

    protected $guarded = ['id', 'created_at'];
}
