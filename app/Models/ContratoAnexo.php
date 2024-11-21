<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoAnexo extends Model
{
    use HasFactory;

    protected $table = 'contrato_obs_anexo';
    protected $guarded = ['id', 'created_at'];

}
