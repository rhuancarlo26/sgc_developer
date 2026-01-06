<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Requests\ImportarRequest;
// CORREÇÃO: importar o PontoService do namespace SGC (onde você colocou a implementação)
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\SgcPmqaCampanha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class ImportarController extends Controller
{
    public function __construct(private readonly PontoService $pontoService)
    {
    }

    public function index(Contrato $contrato, SgcPmqaCampanha $campanha, ImportarRequest $request): RedirectResponse
    {
        $file = $request->file('arquivo');

        $response = $this->pontoService->importarParaCampanha($campanha, $file);

        return to_route('contratos.contratada.servicos.pmqa.configuracao.ponto.index', [
            'contrato' => $contrato->id,
            'campanha' => $campanha->id
        ])->with('message', $response);
    }
}
