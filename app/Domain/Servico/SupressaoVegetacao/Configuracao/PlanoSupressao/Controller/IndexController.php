<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\app\Utils\ConfigucacaoParecer;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly PlanoSupressaoService $planoSupressaoService,
        private readonly ConfigucacaoParecer $configucacaoParecer
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Configuracao/PlanoSupressao/Index', props: [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'data' => $this->planoSupressaoService->index(servico: $servico),
            ...$this->configucacaoParecer->get($servico->id)
        ]);
    }

}
