<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Requests\StoreRetRequest;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Requests\StoreRHRequest;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreRetController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRetRequest $request)
    {
        $response = $this->campanhaService->storeRet($request->validated());

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'campanha' => $request->validated('id_campanha')
        ])->with('message', $response['request']);
    }
}
