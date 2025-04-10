<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaShapefile extends Model
{
  use HasFactory;

  protected $table = 'licenca_shapefile';

  protected $guarded = ['id', 'created_at'];
}
