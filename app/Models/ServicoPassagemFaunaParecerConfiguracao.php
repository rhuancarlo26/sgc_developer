<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPassagemFaunaParecerConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_parecer_configuracao';
    protected $guarded = ['id'];
}
