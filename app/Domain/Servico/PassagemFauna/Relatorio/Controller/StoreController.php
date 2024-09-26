<?php

namespace App\Domain\Servico\PassagemFauna\Relatorio\Controller;

use App\Domain\Servico\PassagemFauna\Relatorio\Requests\StoreRequest;
use App\Domain\Servico\PassagemFauna\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, StoreRequest $request)
    {
        $response = $this->relatorioService->store($request->validated());

        return to_route('contratos.contratada.servicos.passagem_fauna.relatorio.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
