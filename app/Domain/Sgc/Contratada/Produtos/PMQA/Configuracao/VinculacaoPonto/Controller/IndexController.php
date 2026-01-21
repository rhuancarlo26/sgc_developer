<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Controller;

use App\Domain\Servico\PMQA\app\Utils\ConfigucacaoParecer;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Services\VinculacaoPontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(
        private readonly VinculacaoPontoService $vinculacaoPontoService,
        private readonly ConfigucacaoParecer $configucacaoParecer
    )
    {
    }

    public function index(Contrato $contrato, SgcPmqa $pmqa, Request $request): Response
    {
        $searchParams = $request->all('columns', 'value');

        $response = $this->vinculacaoPontoService->index($pmqa, $searchParams);
        return Inertia::render('Servico/PMQA/Configuracao/VinculacaoPonto/Index', [
            'contrato' => $contrato,
            'pmqa'  => $pmqa->load(['tipo', 'pmqa_config_lista_parecer']),
            ...$response,
            ...$this->configucacaoParecer->get($pmqa->id)
        ]);
    }
}
