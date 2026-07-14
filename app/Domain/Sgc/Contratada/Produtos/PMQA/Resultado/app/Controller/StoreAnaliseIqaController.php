<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests\StoreAnaliseIqaRequest;
use App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaResultado;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreAnaliseIqaController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, SgcPmqa $pmqa, SgcPmqaResultado $resultado, StoreAnaliseIqaRequest $request): RedirectResponse
    {
        $image = $request->validated('graf_analise_iqa');

        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

        $imageData = base64_decode($image);

        $post = [
            ...$request->validated(),
            'graf_analise_iqa' => $imageData
        ];

        $response = $this->resultadoService->storeAnaliseIqa($post);

        return to_route('contratos.contratada.sgc.pmqa.resultado.resultado', ['contrato' => $contrato->id, 'pmqa' => $pmqa->id, 'resultado' => $resultado->id])->with('message', $response['request']);
    }
}
