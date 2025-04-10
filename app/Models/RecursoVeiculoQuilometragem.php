<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoVeiculoQuilometragem extends Model
{
  use HasFactory;

  protected $table = 'recurso_veiculo_quilometragens';
  protected $guarded = ['id', 'created_at'];
}
