<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Controller;

use App\Domain\Servico\PassagemFauna\Resultado\Requests\UpdateAnaliseRequest;
use App\Domain\Servico\PassagemFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class UpdateAnaliseController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaResultado $resultado, UpdateAnaliseRequest $request)
    {
        $response = $this->resultadoService->updateAnalise(post: $request->validated());

        return to_route('contratos.contratada.servicos.passagem_fauna.resultado.resultado', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'resultado' => $resultado->id
        ])->with('message', $response['request']);
    }
}
