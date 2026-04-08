<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'campos' => 'array'
    ];
}
