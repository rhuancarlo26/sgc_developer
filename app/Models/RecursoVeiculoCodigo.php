<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoVeiculoCodigo extends Model
{
    use HasFactory;

    protected $table = 'codigo_veiculos';
    protected $guarded = ['id', 'created_at'];
}
