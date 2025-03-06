<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaConfigArmadilhaMetodoImagem extends Model
{
    use HasFactory;

    protected $table = 'fauna_config_armadilhas_metodos_imagem';
    protected $guarded = ['id'];
}
