<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaDocumento extends Model
{
    use HasFactory;

    protected $table = 'licenca_documento';

    protected $guarded = ['id', 'created_at'];
}