<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller;

use App\Domain\Servico\PMQA\Execucao\Coleta\app\Requests\StoreArquivoRequest;
use App\Domain\Servico\PMQA\Execucao\Coleta\app\Services\ColetaService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreArquivoController extends Controller
{
    public function __construct(private readonly ColetaService $coletaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, StoreArquivoRequest $request): RedirectResponse
    {
        $response = $this->coletaService->storeArquivo($request->validated());

        return to_route('contratos.contratada.servicos.pmqa.execucao.coleta.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'campanha' => $campanha->id,
            'ponto' => $request->fk_campanha_ponto
        ])->with('message', $response['request']);
    }
}
