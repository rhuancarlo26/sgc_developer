<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEspeleoJustificativa extends Model
{
    protected $table = 'sgc_espeleo_campanha_justificativas';
    protected $fillable = ['campanha_id', 'id_contrato', 'titulo', 'justificativa', 'tipo'];
}