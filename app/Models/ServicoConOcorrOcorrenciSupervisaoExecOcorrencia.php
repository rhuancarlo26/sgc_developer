<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoConOcorrOcorrenciSupervisaoExecOcorrencia extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_ocorrencia';
    protected $guarded = ['id'];
    protected $casts = [
        'indicio_responsabilidade' => 'bool',
        'rnc_direto' => 'bool'
    ];

    public function rodovia()
    {
        return $this->belongsTo(Rodovia::class, 'id_rodovia');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(ServicoContOcorrSupervisaoConfigLote::class, 'id_lote');
    }

    public function registros(): HasMany
    {
        return $this->hasMany(ServicoConOcorrSupervicaoExecOcorrenciaRegistro::class, 'id_ocorrencia');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(ServicoConOcorrSupervisaoExecOcorrenciaHistorico::class, 'id_ocorrencia');
    }

    public function vistorias(): HasMany
    {
        return $this->hasMany(ServicoConOcorrSupervisaoExecOcorrenciaVistoria::class, 'id_ocorrencia');
    }

    public function ocorrencia_anterior()
    {
        return $this->hasOneThrough(ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class, ServicoConOcorrSupervisaoExecOcorrenciaAnterior::class, 'id_ocorrencia_vigente', 'id', 'id', 'id_ocorrencia_anterior');
    }
}
