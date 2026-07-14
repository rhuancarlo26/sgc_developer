<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaCampanhaPontoMedicaoArquivo extends Model
{
    use HasFactory;

    protected $table   = 'sgc_pmqa_ponto_medicao_laudo';
    protected $guarded = ['id', 'created_at'];

    public function medicao()
    {
        return $this->belongsTo(related: SgcPmqaCampanhaPontoMedicao::class, foreignKey: 'pmqa_ponto_medicao_id');
    }
}
