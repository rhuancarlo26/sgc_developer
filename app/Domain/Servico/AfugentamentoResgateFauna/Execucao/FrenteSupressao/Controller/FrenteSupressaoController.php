<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;

class FrenteSupressaoController extends Controller
{
    public function __construct()
    {
    }

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        $estados = $contrato->ufs;
        $rodovias = $contrato->brs;
        // dd($rodovias);
        return Inertia::render('Servico/AfugentamentoResgateFauna/Execucao/FrenteSupressao', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            // ...$response
        ]);
    }
}