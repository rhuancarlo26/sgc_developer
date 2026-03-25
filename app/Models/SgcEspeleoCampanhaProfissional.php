<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEspeleoCampanhaProfissional extends Model
{
    protected $table = 'sgc_espeleo_campanha_profissionais';
    protected $fillable = ['campanha_id', 'id_modulo', 'id_contrato', 'profissional_id'];
}