<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services\PontoService; // <-- service SGC
use App\Models\Contrato;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaPonto;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly PontoService $pontoService)
    {
    }

    public function index(Contrato $contrato, SgcPmqaCampanha $campanha, SgcPmqaPonto $ponto): RedirectResponse
    {
        $response = $this->pontoService->deletePonto($ponto);

        return to_route('contratos.contratada.sgc.pmqa.configuracao.ponto.index', [
            'contrato' => $contrato->id,
            'campanha' => $campanha->id
        ])->with('message', $response['content'] ?? $response);
    }
}
