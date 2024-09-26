<?php

namespace App\Domain\Servico\PassagemFauna\Relatorio\Controller;

use App\Domain\Servico\PassagemFauna\Relatorio\Requests\UpdateRequest;
use App\Domain\Servico\PassagemFauna\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class UpdateController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request)
    {
        $response = $this->relatorioService->update($request->validated());

        return to_route('contratos.contratada.servicos.passagem_fauna.relatorio.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
