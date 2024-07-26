<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaRequerimento extends Model
{
  use HasFactory;

  protected $table = 'arquivo_requerimento';

  protected $guarded = ['id', 'created_at', 'updated_at'];
}
