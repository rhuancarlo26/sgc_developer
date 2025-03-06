<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoLicencaObservacao extends Model
{
    use HasFactory;

    protected $table = 'contrato_licenca_obs';
    protected $guarded = ['id', 'created_at'];
}
