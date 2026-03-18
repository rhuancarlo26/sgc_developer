<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests\UpdateAnaliseRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaResultado;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateAnaliseController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, SgcPmqa $pmqa, SgcPmqaResultado $resultado, UpdateAnaliseRequest $request): RedirectResponse
    {
        $image = $request->validated('graf_analise_parametro');

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $imageData = base64_decode($image);

        $post = [
            'sgc_resultado_id' => $request->validated('sgc_resultado_id'),
            'parametro_id' => $request->validated('parametro_id'),
            'analise_parametro' => $request->validated('analises')[$request->validated('fk_parametro')],
            'graf_analise_parametro' => $imageData
        ];

        $response = $this->resultadoService->updateAnalises($post);

        return to_route('contratos.contratada.sgc.pmqa.resultado.resultado', ['contrato' => $contrato->id, 'pmqa' => $pmqa->id, 'resultado' => $resultado->id])->with('message', $response['request']);
    }
}
