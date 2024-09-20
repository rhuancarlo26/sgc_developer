<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Requests\StoreAbioRequest;
use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreAbioController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreAbioRequest $request)
    {
        $response = $this->campanhaService->storeAbio($request->validated());

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.campanha.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'campanha' => $request->validated('id_campanha')
        ])->with('message', $response['request']);
    }
}
