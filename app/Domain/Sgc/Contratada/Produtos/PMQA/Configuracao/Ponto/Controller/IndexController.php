<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaPonto;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    public function index(
        int $contrato,
        string $produto,
        int $pmqa,
        Request $request
    ): Response {
        $contratoModel = Contrato::findOrFail($contrato);
        $pmqaModel = SgcPmqa::findOrFail($pmqa);

        // 🔹 Pontos
        $pontos = SgcPmqaPonto::where('pmqa_id', $pmqaModel->id)
            ->orderBy('id')
            ->get();

        // 🔹 Parâmetros (via service)
        $searchParams = $request->only(['columns', 'value']);
        $tabParametros = $this->parametroService->index($pmqaModel, $searchParams);

        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Create', [
            'contrato'  => $contratoModel->id,
            'contratos' => $contratoModel,
            'produto'   => ['slug' => $produto],
            'pmqa'      => $pmqaModel,
            'servico'   => $pmqaModel, // 👈 importante para IndexParametros / modal

            'pontos'    => $pontos,

            // ✅ ESSENCIAL
            ...$tabParametros,

            'subStep'   => (int) $request->query('subStep', 2),
            'tab'       => $request->query('tab', 'configuracao'),
        ]);
    }
}
