<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaCampanhaPontoColetaArquivo extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_campanha_ponto_coleta_arquivos';
    protected $guarded = ['id', 'created_at'];

    public function coleta()
    {
        return $this->belongsTo(ServicoPmqaCampanhaPontoColeta::class, 'coleta_id');
    }
}