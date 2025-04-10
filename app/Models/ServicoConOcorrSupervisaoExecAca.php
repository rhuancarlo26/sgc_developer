<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServicoConOcorrSupervisaoExecAca extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_aca';
    protected $guarded = ['id'];

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servicos::class, 'id_servico');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(ServicoContOcorrSupervisaoConfigLote::class, 'id_lote');
    }

    public function rncs(): BelongsToMany
    {
        return $this->belongsToMany(ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::class, 'supervisao_exec_aca_rnc', 'id_aca', 'id_ocorrencia', 'id', 'id');
    }
}
