<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaCondicionante extends Model
{
  use HasFactory;

  protected $table = 'licenca_condicionantes';

  protected $guarded = ['id', 'created_at', 'updated_at'];
}