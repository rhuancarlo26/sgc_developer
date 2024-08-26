<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Controller;

use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\ServicoConOcorrSupervisaoResultadoOutrasAnalises;
use App\Models\Servicos;

class VisualizarOutraAnalise
{
    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoResultado $resultado, ServicoConOcorrSupervisaoResultadoOutrasAnalises $outraAnalise)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $outraAnalise->caminho_arquivo);
    }
}
