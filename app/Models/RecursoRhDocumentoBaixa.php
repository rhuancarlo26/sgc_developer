<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoRhDocumentoBaixa extends Model
{
    use HasFactory;

    protected $table = 'recurso_rh_documento_baixas';
    protected $guarded = ['id', 'created_at'];
}
