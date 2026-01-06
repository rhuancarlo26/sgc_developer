<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\app\Utils\ConfigucacaoParecer;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly ParametroService $parametroService,
        private readonly ConfigucacaoParecer $configucacaoParecer
    )
    {
    }

    public function index(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->parametroService->index($servico, $searchParams);

        return Inertia::render('Servico/PMQA/Configuracao/Parametro/Index', [
            'contrato' => $contrato,
            'servico'  => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
            ...$response,
            ...$this->configucacaoParecer->get($servico->id)
        ]);
    }
}
