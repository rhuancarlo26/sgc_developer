<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoHistorico extends Model
{
    use HasFactory;

    protected $table = 'contrato_observacao';
    protected $guarded = ['id', 'created_at'];

}
