<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcComentario extends Model
{
    use HasFactory;

    protected $table = 'sgc_comentarios';
    protected $guarded = ['id', 'created_at'];
}
