<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoParecerAfugentamentoConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'afugent_fauna_parecer_configuracao';
    protected $guarded = ['id', 'created_at'];
}
