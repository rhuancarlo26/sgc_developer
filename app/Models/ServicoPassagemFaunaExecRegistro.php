<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaExecRegistro extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_exec_registros';
    protected $guarded = ['id'];
}
