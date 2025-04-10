<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Service\RelatorioService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class RelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService) {}

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        $relatorio = $this->relatorioService->getRelatorio($servico);
        return Inertia::render('Servico/AfugentamentoResgateFauna/Relatorio/Relatorio', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo', 'parecerAfugentamento']),
            'relatorio' => $relatorio,
        ]);
    }
}
