<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Models\Contrato;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaPonto;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    /**
     * @param Contrato $contrato   — rota: {contrato}
     * @param SgcPmqaCampanha $campanha — rota: {campanha}
     * @param SgcPmqaPonto|null $ponto  — rota opcional: {ponto}
     */
    public function index(Contrato $contrato, SgcPmqaCampanha $campanha, ?SgcPmqaPonto $ponto = null): Response
    {
        // carregue relações se precisar (ex.: campanha->parametros)
        $campanha->load(['parametros']);

        return Inertia::render('Servico/PMQA/Configuracao/Ponto/Form', [
            'contrato' => $contrato,
            'campanha' => $campanha,
            'ponto' => $ponto,
        ]);
    }
}
