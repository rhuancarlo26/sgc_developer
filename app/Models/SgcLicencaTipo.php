<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcLicencaTipo extends Model
{
    protected $table = 'sgc_licencas_tipos';

    protected $fillable = ['sigla', 'nome', 'regularizacao', 'ativo'];

    protected $casts = [
        'regularizacao' => 'boolean',
        'ativo' => 'boolean',
    ];
}
