<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoVeiculoCodigo extends Model
{
    use HasFactory;

    protected $table = 'recurso_veiculo_codigos';
    protected $guarded = ['id', 'created_at'];
}