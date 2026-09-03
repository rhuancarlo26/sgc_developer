<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Coleta\app\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaCampanhasPonto;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function index(
        Contrato $contrato,
        string $produto,
        SgcPmqa $pmqa,
        SgcPmqaCampanha $campanha,
        SgcPmqaCampanhasPonto $ponto
    ): Response {
        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Execucao/Coleta/Form', [
            'contrato' => $contrato,
            'pmqa' => $pmqa,
            'campanha' => $campanha,
            'ponto' => $ponto->load(['ponto', 'coleta.arquivos']),
            'produto' => $produto,
            'canApprove' => auth()->user()->hasAnyRole(['Administrador', 'Fiscal'])
        ]);
    }
}
