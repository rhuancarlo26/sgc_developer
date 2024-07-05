<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanhaPontoMedicaoArquivo extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_campanha_ponto_medicao_arquivos';
    protected $guarded = ['id', 'created_at'];

    public function medicao()
    {
        return $this->belongsTo(ServicoPmqaCampanhaPontoMedicao::class, 'medicao_id');
    }
}