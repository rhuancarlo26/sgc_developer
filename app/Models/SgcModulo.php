<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SgcModulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sgc_modulos';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'campos' => 'array'
    ];
}
