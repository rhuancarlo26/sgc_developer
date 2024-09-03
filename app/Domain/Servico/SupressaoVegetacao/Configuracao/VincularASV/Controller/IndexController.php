<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Controller;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Domain\Servico\SupressaoVegetacao\app\Utils\ConfigucacaoParecer;
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
        private readonly LicencaService      $licencaService,
        private readonly VincularASVService  $vincularASVService,
        private readonly ConfigucacaoParecer $configucacaoParecer
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Configuracao/VincularASV/Index', props: [
            'data' => $this->vincularASVService->index(servico: $servico),
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'licencas' => $this->licencaService->all(searchParams: ['tipo', '3']),
            ...$this->configucacaoParecer->get($servico->id)
        ]);
    }
}
