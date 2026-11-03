<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcLicencaAlerta extends Model
{
    public $timestamps = false;

    protected $table = 'sgc_licencas_alertas';

    protected $fillable = ['prazo_id', 'marco', 'enviado_em'];

    protected $casts = ['enviado_em' => 'datetime'];
}
