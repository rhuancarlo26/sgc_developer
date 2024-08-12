<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultadoSupressao extends Model
{
    use SoftDeletes;

    protected $table = 'resultado_supressao';

    protected $guarded = ['id'];
}
