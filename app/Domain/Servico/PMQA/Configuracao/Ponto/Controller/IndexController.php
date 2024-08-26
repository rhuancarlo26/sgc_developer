<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Servico\PMQA\app\Utils\ConfigucacaoParecer;

class IndexController extends Controller
{
    public function __construct(
        private readonly PontoService $pontoService,
        private readonly ConfigucacaoParecer $configucacaoParecer
    )
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->pontoService->index($servico, $searchParams);

        return Inertia::render('Servico/PMQA/Configuracao/Ponto/Index', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            ...$response,
            ...$this->configucacaoParecer->get($servico->id)
        ]);
    }
}
