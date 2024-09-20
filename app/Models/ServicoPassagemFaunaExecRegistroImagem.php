<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaExecRegistroImagem extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_exec_registros_imagem';
    protected $guarded = ['id'];
}
