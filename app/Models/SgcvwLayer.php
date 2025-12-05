<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcvwLayer extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'mbtiles_path',
        'status',
        'error'
    ];
}
