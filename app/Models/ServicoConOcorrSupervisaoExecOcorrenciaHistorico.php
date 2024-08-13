<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoConOcorrSupervisaoExecOcorrenciaHistorico extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_ocorrencia_historico';
    protected $guarded = ['id'];

    public function levantamento(): BelongsTo
    {
        return $this->belongsTo(ServicoConOcorrSupervisaoExecOcorrenciaLevantamento::class, 'id_ocorrencia_levantamento');
    }
}
