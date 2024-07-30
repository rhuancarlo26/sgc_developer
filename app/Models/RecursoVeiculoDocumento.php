<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoVeiculoDocumento extends Model
{
    use HasFactory;

    protected $table = 'veiculo_arquivo';
    protected $guarded = ['id', 'created_at'];
}
