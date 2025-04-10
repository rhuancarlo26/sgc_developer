<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteController extends Controller
{
    public function __construct(private readonly PontoService $pontoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaPonto $ponto): RedirectResponse
    {
        $response = $this->pontoService->deletePonto($ponto);

        return to_route('contratos.contratada.servicos.pmqa.configuracao.ponto.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
