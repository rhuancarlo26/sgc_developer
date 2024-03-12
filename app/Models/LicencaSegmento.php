<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaSegmento extends Model
{
  use HasFactory;

  protected $table = 'licenca_segmento';

  protected $guarded = ['id', 'created_at'];

  protected $fillable = [
    'licenca_id',
    'rodovia',
    'uf_inicial',
    'uf_final',
    'km_inicial',
    'km_final',
    'extensao',
  ];
}