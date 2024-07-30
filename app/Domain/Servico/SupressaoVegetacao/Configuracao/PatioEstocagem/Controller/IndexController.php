<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Controller;

use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services\PatioEstocagemService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services\TipoPatioService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services\VincularASVService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly TipoPatioService $tipoPatioService,
        private readonly VincularASVService $vincularASVService,
        private readonly PatioEstocagemService $patioEstocagemService,
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Configuracao/PatioEstocagem/Index', props: [
            'contrato' => $contrato,
            'data' => $this->patioEstocagemService->index($servico),
            'servico' => $servico->load(['tipo']),
            'tipos' => $this->tipoPatioService->all(['id', 'nome']),
            'licencas' => $this->vincularASVService->search('servico_id', $servico->id)
                ->with(['licenca.tipo'])
                ->get(),
        ]);
    }

}
