<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecCampanhasRet;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteRetController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecCampanhasRet $campanha_ret)
    {
        $response = $this->campanhaService->deleteRet($campanha_ret);

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'campanha' => $campanha_ret->id_campanha
        ])->with('message', $response['request']);
    }
}
