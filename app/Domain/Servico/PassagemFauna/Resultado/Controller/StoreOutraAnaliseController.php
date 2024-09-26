<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Controller;

use App\Domain\Servico\PassagemFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreOutraAnaliseController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoResultado $resultado, Request $request)
    {
        $response = $this->resultadoService->storeOutraAnalise(post: $request->all());

        return to_route('contratos.contratada.servicos.passagem_fauna.resultado.resultado', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'resultado' => $resultado->id
        ])->with('message', $response['request']);
    }
}
