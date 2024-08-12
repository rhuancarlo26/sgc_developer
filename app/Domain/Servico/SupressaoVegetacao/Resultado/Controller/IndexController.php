<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Controller;


use App\Domain\Servico\SupressaoVegetacao\Resultado\Enums\Periodo;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly ResultadoService $resultadoService,
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Resultado/Index', props: [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'data' => $this->resultadoService->index(servico: $servico),
            'periodos' => Periodo::toArray(),
        ]);
    }

}
