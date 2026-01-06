<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\SgcPmqaCampanha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Servico\PMQA\app\Utils\ConfigucacaoParecer;

class IndexController extends Controller
{
    public function __construct(
        private readonly PontoService        $pontoService,
        private readonly ConfigucacaoParecer $configucacaoParecer
    )
    {
    }

    public function index(Contrato $contrato, SgcPmqaCampanha $campanha, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        // adaptar o service para aceitar campanha em vez de servico
        $response = $this->pontoService->indexParaCampanha($campanha, $searchParams);

        return Inertia::render('Servico/PMQA/Configuracao/Ponto/Index', [
            'contrato' => $contrato,
            'campanha' => $campanha->load(['parametros']),
            ...$response,
            ...$this->configucacaoParecer->get($campanha->id)
        ]);
    }
}
