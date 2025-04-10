<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaResultadoAnalise extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_resultado_analises';
    protected $guarded = ['id'];
}
