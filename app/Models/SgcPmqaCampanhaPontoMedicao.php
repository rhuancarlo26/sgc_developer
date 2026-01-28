<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcPmqaCampanhaPontoMedicao extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_ponto_medicao';
    protected $guarded = ['id', 'created_at'];
    protected $casts = ['medido' => 'bool'];

    public function parametros()
    {
        return $this->HasMany(related: SgcPmqaCampanhaPontoMedicaoParametro::class, foreignKey: 'pmqa_ponto_medicao_id');
    }

    public function arquivos()
    {
        return $this->hasMany(related: SgcPmqaCampanhaPontoMedicaoArquivo::class, foreignKey: 'pmqa_ponto_medicao_id');
    }
}
