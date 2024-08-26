<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaParametro extends Model
{
    use HasFactory;

    protected $table   = 'parametros';
    protected $guarded = ['id', 'created_at'];
}
