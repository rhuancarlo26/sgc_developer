<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\app\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\app\Services\FiscalService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class EnviaFiscalController extends Controller
{
    public function __construct(private readonly FiscalService $fiscalService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): RedirectResponse
    {
        $response = $this->fiscalService->enviaFiscal($request->all(), $servico->id);

        return to_route('contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.abio.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
