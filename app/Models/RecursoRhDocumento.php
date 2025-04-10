<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecursoRhDocumento extends Model
{
    use HasFactory;

    protected $table = 'rh_arquivo';
    protected $guarded = ['id', 'created_at'];
}
