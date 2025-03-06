<?php

namespace App\Domain\Servico\app\Controller;

use App\Domain\Servico\app\Services\ServicoService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnviaServicoFiscalController extends Controller
{
    public function __construct(private readonly ServicoService $servicoService)
    {
    }

    public function index(Servicos $servico)
    {
        $servico['status_aprovacao'] = 2;
        $this->servicoService->updateServico($servico->toArray());

        return to_route('contratos.contratada.servicos.index', [
            'contrato' => $servico->id_contrato
        ])->with('message', ['type' => 'success', 'content' => 'Enviado para o fiscal!']);
    }
}
