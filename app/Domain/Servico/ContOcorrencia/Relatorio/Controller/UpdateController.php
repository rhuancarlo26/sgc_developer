<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Controller;


use App\Domain\Servico\ContOcorrencia\Relatorio\Requests\UpdateRequest;
use App\Domain\Servico\ContOcorrencia\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class UpdateController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, UpdateRequest $request): RedirectResponse
    {
        $response = $this->relatorioService->update($request->validated());

        return to_route('contratos.contratada.servicos.cont_ocorrencia.relatorio.index', ['contrato' => $contrato->id, 'servico' => $servico->id])->with('message', $response['request']);
    }
}
