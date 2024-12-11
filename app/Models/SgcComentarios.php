<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcComentarios extends Model
{
    use HasFactory;

    protected $table = 'sgc_comentarios';
    protected $guarded = ['id', 'created_at'];
}
