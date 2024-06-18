<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaParametroLista extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_parametro_listas';
    protected $guarded = ['id', 'created_at'];
}
