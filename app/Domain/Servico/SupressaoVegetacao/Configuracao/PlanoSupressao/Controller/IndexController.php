<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Controller;

use App\Domain\Servico\SupressaoVegetacao\app\Utils\ConfigucacaoParecer;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\TipoBiomaService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly TipoBiomaService          $tipoBiomaService,
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
            'biomas' => $this->tipoBiomaService->all(columns: ['id', 'nome']),
            'data' => $this->planoSupressaoService->index(servico: $servico),
            ...$this->configucacaoParecer->get($servico->id)
        ]);
    }

}
