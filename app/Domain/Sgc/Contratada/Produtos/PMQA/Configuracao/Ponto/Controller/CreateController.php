<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaPonto;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function index(Contrato $contrato, SgcPmqa $campanha, ?SgcPmqaPonto $ponto = null): Response
    {
        // Carrega relações necessárias
        $campanha->load(['parametros']);

        // Carrega TODOS os pontos da campanha (ou da pmqa/campanha)
        $pontos = SgcPmqaPonto::where('campanha_id', 5)  // ou 'fk_pmqa' se for esse o campo
            ->orderBy('id')  // ou 'chave', 'nome_ponto_coleta'
            ->get();

        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Components/ImportarPontos', [
            'contrato' => $contrato,
            'campanha' => $campanha,
            'ponto'    => $ponto,
            'pontos'   => $pontos,  // ← Array de pontos para a tabela
        ]);
    }
}
