<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaSegmento extends Model
{
  use HasFactory;

  protected $table = 'licenca_segmento';

  protected $guarded = ['id', 'created_at'];

}