<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoContOcorrSupervisaoParecerConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'supervisao_parecer_configuracao';
    protected $guarded = ['id'];
}
