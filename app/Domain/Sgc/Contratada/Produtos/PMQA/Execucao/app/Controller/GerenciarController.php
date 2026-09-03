<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\app\Services\CampanhaPontoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaExecCampanha;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GerenciarController extends Controller
{
    public function __construct(private readonly CampanhaPontoService $campanhaPontoService) {}

    public function index(
        Contrato $contrato,
        string $produto,
        SgcPmqa $pmqa,
        SgcPmqaExecCampanha $campanha,
        Request $request
    ): Response {
        $searchParams = $request->all('columns', 'value');

        $response = $this->campanhaPontoService->index($campanha, $searchParams);

        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Execucao/Gerenciar', [
            'contrato'  => $contrato,
            'produto'   => ['slug' => request()->route('produto')], // ou $produto se estiver na assinatura
            'pmqa'      => $pmqa,
            'campanha'  => $campanha,
            'canApprove' => auth()->user()->hasAnyRole(['Administrador', 'Fiscal']),
            ...$response
        ]);
    }
}
