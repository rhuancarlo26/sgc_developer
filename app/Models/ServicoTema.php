<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoTema extends Model
{
    use HasFactory;

    protected $table = 'temas';
    protected $guarded = ['id', 'created_at'];
}
