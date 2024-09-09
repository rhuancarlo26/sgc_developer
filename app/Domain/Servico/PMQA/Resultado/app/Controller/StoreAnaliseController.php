<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Controller;

use App\Domain\Servico\PMQA\Resultado\app\Requests\StoreAnaliseRequest;
use App\Domain\Servico\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreAnaliseController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaResultado $resultado, StoreAnaliseRequest $request): RedirectResponse
    {
        $image = $request->validated('graf_analise_parametro');

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $imageData = base64_decode($image);

        $post = [
            'fk_resultado' => $request->validated('fk_resultado'),
            'fk_parametro' => $request->validated('fk_parametro'),
            'analise_parametro' => $request->validated('analises')[$request->validated('fk_parametro')],
            'graf_analise_parametro' => $imageData
        ];

        $response = $this->resultadoService->storeAnalises($post);

        return to_route('contratos.contratada.servicos.pmqa.resultado.resultado', ['contrato' => $contrato->id, 'servico' => $servico->id, 'resultado' => $resultado->id])->with('message', $response['request']);
    }
}
