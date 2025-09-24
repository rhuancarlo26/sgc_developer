<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEspeleoJustificativa extends Model
{
    protected $table = 'sgc_espeleo_campanha_justificativas';
    protected $fillable = ['campanha_id', 'codigo_sei', 'id_contrato', 'titulo', 'justificativa', 'tipo'];
}