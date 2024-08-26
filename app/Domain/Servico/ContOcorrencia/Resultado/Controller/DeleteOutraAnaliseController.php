<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Controller;

use App\Domain\Servico\ContOcorrencia\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\ServicoConOcorrSupervisaoResultadoOutrasAnalises;
use App\Models\Servicos;
use Illuminate\Http\RedirectResponse;

class DeleteOutraAnaliseController
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoResultado $resultado, ServicoConOcorrSupervisaoResultadoOutrasAnalises $outraAnalise): RedirectResponse
    {
        $response = $this->resultadoService->destroyOutraAnalise(outra_analise: $outraAnalise);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.resultado.resultado', ['contrato' => $contrato->id, 'servico' => $servico->id, 'resultado' => $resultado->id])->with('message', $response['request']);
    }
}
