<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Pareceres\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Pareceres\Service\PareceresService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;

class PareceresController extends Controller
{
    public function __construct(private readonly PareceresService $pareceresService) {}

    public function index(Contrato $contrato, Servicos $servico)
    {
        $pareceres = $this->pareceresService->getPareceres($servico->id);
        return Inertia::render('Servico/AfugentamentoResgateFauna/Pareceres/Pareceres', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo', 'parecerAfugentamento']),
            'pareceres' => $pareceres,
        ]);
    }
}
