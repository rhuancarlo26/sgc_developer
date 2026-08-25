<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Controller;

use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPonto;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaCampanhasPonto;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, SgcPmqaCampanha $campanha, SgcPmqaCampanhasPonto $ponto): Response
    {
        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Execucao/Medir/Form', [
            'contrato' => $contrato,
            'produto' => $produto,
            'pmqa'  => $pmqa,
            'campanha' => $campanha,
            'ponto'    => $ponto->load([
                'ponto.lista.parametros_vinculados.parametro',
                'medicao.arquivos',
                'medicao.parametros'
            ]),
            'canApprove' => auth()->user()->hasAnyRole(['Administrador', 'Fiscal'])
        ]);
    }
}
