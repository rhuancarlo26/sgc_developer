<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecCampanha $campanha)
    {
        $response = $this->campanhaService->destroy($campanha);

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
