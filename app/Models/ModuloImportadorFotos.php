<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloImportadorFotos extends Model
{
    use HasFactory;

    protected $table = 'modulo_importador_fotos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
