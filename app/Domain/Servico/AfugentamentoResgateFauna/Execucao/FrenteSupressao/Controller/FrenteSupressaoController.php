<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Service\FrenteSupressaoService;
use App\Models\AfugentFaunaExecFrenteModel;
use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;

class FrenteSupressaoController extends Controller
{
    public function __construct(private readonly FrenteSupressaoService $frenteSupressaoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        $frenteSupressao = $this->frenteSupressaoService->getFrenteSupressao($servico);
        return Inertia::render('Servico/AfugentamentoResgateFauna/Execucao/FrenteSupressao', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            'frenteSupressao' => $frenteSupressao->load(['rodovia', 'ufInicial', 'ufFinal']),
        ]);
    }
}
