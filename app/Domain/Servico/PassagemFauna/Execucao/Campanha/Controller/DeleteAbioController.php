<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecCampanhasAbio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteAbioController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecCampanhasAbio $campanha_abio)
    {
        $response = $this->campanhaService->deleteAbio($campanha_abio);

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'campanha' => $campanha_abio->id_campanha
        ])->with('message', $response['request']);
    }
}
