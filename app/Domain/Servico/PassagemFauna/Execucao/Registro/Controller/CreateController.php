<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecRegistro;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __construct(private readonly RegistroService $registroService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecRegistro $registro): Response
    {
        $response = $this->registroService->create($servico);

        return Inertia::render('Servico/PassagemFauna/Execucao/Registro/Form', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'registro' => $registro,
            ...$response
        ]);
    }
}
