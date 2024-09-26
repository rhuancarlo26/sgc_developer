<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Controller;

use App\Domain\Servico\PassagemFauna\Resultado\Services\ResultadoService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaResultado;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResultadoController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaResultado $resultado): Response
    {
        $response = $this->resultadoService->resultado($resultado);

        return Inertia::render('Servico/PassagemFauna/Resultado/Resultado', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo', 'parecer_passagem_fauna']),
            ...$response
        ]);
    }
}
