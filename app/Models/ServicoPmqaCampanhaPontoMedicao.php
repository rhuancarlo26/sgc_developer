<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoPmqaCampanhaPontoMedicao extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_campanha_ponto_medicoes';
    protected $guarded = ['id', 'created_at'];
    protected $casts = [
        'sem_coleta' => 'bool'
    ];

    public function parametros()
    {
        return $this->HasMany(ServicoPmqaCampanhaPontoMedicaoParametro::class, 'ponto_medicao_id');
    }

    public function arquivos()
    {
        return $this->hasMany(ServicoPmqaCampanhaPontoMedicaoArquivo::class, 'medicao_id');
    }
}