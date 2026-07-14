<?php

namespace App\Domain\Servico\PMQA\app\Controller;

use App\Domain\Servico\PMQA\app\Services\FiscalService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class EnviaFiscalController extends Controller
{
    public function __construct(private FiscalService $fiscalService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): RedirectResponse
    {
        $response = $this->fiscalService->enviaFiscal($request->all(), $servico->id);

        return to_route('contratos.contratada.servicos.pmqa.configuracao.vinculacao_ponto.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
