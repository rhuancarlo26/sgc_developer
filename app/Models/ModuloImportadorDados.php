<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloImportadorDados extends Model
{
    use HasFactory;

    protected $table = 'modulo_importador_dados';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'dados' => 'array'
    ];
}
