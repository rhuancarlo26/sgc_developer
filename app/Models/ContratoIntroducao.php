<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoIntroducao extends Model
{
    use HasFactory;

    protected $table = 'contrato_introducao';

    protected $guarded = ['id', 'created_at'];
}
