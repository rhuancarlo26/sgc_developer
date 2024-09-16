<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecCampanhasRh;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteRhController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecCampanhasRh $campanha_rh)
    {
        $response = $this->campanhaService->deleteRh($campanha_rh);

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'campanha' => $campanha_rh->id_campanha
        ])->with('message', $response['request']);
    }
}
