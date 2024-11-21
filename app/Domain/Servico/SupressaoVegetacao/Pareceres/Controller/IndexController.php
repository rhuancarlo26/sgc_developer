<?php

namespace App\Domain\Servico\SupressaoVegetacao\Pareceres\Controller;


use App\Domain\Servico\SupressaoVegetacao\Pareceres\Services\ParecerService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

    public function __construct(
        private readonly ParecerService $parecerService
    )
    {
    }

    public function __invoke(Contrato $contrato, Servicos $servico, Request $request): Response
    {
        return Inertia::render(component: 'Servico/SupressaoVegetacao/Pareceres/Index', props: [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'data' => $this->parecerService->getPareceres($servico->id)
        ]);
    }

}
