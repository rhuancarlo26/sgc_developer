<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoParecerPMQAConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'pmqa_parecer_configuracao';
    protected $guarded = ['id', 'created_at'];
}
