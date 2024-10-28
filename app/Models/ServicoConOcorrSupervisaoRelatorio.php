<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupervisaoRelatorio extends Model
{
    use HasFactory;

    protected $table = 'supervisao_relatorio';
    protected $guarded = ['id'];

    public function resultado()
    {
        return $this->belongsTo(ServicoConOcorrSupervisaoResultado::class, 'id_resultado');
    }

    public function servico()
    {
        return $this->belongsTo(Servicos::class, 'id_servico');
    }
}
