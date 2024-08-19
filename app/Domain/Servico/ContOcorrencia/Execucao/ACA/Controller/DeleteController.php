<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ACA\Controller;

use App\Domain\Servico\ContOcorrencia\Execucao\ACA\Services\ACAService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoExecAca;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteController extends Controller
{
    public function __construct(private readonly ACAService $ACAService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoExecAca $aca)
    {
        $response = $this->ACAService->destroy($aca->id);

        return to_route('contratos.contratada.servicos.cont_ocorrencia.execucao.aca.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
