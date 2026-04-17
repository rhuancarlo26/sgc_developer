<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloImportadorHistorico extends Model
{
    use HasFactory;

    protected $table = 'modulo_importador_historicos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
