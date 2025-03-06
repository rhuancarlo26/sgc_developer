<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaConfigArmadilhaMetodo extends Model
{
    use HasFactory;

    protected $table = 'fauna_config_armadilhas_metodos';
    protected $guarded = ['id'];
}
