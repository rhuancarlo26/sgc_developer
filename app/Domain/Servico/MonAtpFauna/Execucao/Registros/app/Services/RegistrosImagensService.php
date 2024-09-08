<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services;

use App\Models\AtFaunaExecucaoRegistroImagem;
use App\Shared\Abstract\BaseModelService;

class RegistrosImagensService extends BaseModelService
{

    protected string $modelClass = AtFaunaExecucaoRegistroImagem::class;

    public function getImagens(int $registroId)
    {
        return $this->model->where('id_registro', $registroId)->get();
    }
}
