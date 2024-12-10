<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoMonitoraFaunaExecCampanha extends Model
{
    use HasFactory;

    protected $table = 'fauna_exec_campanhas';
    protected $guarded = ['id'];

    public function campanha_abios(): HasMany
    {
        return $this->hasMany(ServicoMonitoraFaunaExecCampanhaAbio::class, 'id_campanha');
    }

    public function profiss_grupo()
    {
        return $this->hasMany(ServicoMonitoraFaunaExecCampanhaProfissGrupo::class, 'id_campanha');
    }
}
