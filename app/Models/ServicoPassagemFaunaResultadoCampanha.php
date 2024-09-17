<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaResultadoCampanha extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_resultado_campanhas';
    protected $guarded = ['id'];
}
