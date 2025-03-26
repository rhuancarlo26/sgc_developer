<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcConsumoDav extends Model
{
  use HasFactory;

  protected $table = 'sgc_consumos_dav';
  protected $guarded = ['id', 'created_at'];
}
