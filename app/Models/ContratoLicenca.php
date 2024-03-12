<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoLicenca extends Model
{
    use HasFactory;

    protected $table = 'contrato_licencas';
    protected $guarded = ['id', 'created_at'];
}