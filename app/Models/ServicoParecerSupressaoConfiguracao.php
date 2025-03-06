<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoParecerSupressaoConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'supressao_parecer_configuracao';
    protected $guarded = ['id', 'created_at'];
}
