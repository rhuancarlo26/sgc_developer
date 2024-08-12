<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoConOcorrSupervisaoExecOcorrenciaVistoria extends Model
{
    use HasFactory;

    protected $table = 'supervisao_exec_ocorrencia_vistoria';
    protected $guarded = ['id'];

    public function imagens(): HasMany
    {
        return $this->hasMany(ServicoConOcorrSupervisaoExecOcorrenciaVistoriaImg::class, 'id_vistoria');
    }

    public function arquivos(): HasMany
    {
        return $this->hasMany(ServicoConOcorrSupervisaoExecOcorrenciaVistoriaArquivoPrazo::class, 'id_vistoria');
    }
}
