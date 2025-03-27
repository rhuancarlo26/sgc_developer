<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaConfigModuloAmostral extends Model
{
    use HasFactory;

    protected $table = 'fauna_config_modulos_amostrais';
    protected $guarded = ['id'];

    public function armadilhas()
    {
        return $this->hasMany(ServicoMonitoraFaunaConfigArmadilhaMetodo::class, 'id_modulo');
    }
}
