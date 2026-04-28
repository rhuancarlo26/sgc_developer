<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaCampanhaPontoColetaArquivo extends Model
{
    use HasFactory;

    protected $table   = 'sgc_pmqa_ponto_coleta_imagem';
    protected $guarded = ['id', 'created_at'];

    public function coleta()
    {
        return $this->belongsTo(related: ServicoPmqaCampanhaPontoColeta::class, foreignKey: 'pmqa_ponto_coleta_id');
    }
}
