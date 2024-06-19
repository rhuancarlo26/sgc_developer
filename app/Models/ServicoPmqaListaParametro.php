<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaListaParametro extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_lista_parametros';
    protected $guarded = ['id', 'created_at'];
}