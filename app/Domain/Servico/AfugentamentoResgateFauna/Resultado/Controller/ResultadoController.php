<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\ResultadoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ResultadoController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        $resultado = $this->resultadoService->getResultado($servico);
        return Inertia::render('Servico/AfugentamentoResgateFauna/Resultado/Resultado', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            'resultado' => $resultado,
        ]);
    }
}