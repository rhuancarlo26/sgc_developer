<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtFaunaGrupoAmostradoModel extends Model
{
    use HasFactory;

    protected $table = 'at_fauna_grupo_amostrado';

    protected $guarded = ['id'];
}
