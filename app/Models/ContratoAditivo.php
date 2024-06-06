<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoAditivo extends Model
{
    use HasFactory;

    protected $table = 'contrato_aditivos';
    protected $guarded = ['id', 'created_at'];
}
