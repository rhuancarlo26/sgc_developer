<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoEquipamentoDocumento extends Model
{
    use HasFactory;

    protected $table = 'recurso_equipamento_documentos';
    protected $guarded = ['id', 'created_at'];
}
