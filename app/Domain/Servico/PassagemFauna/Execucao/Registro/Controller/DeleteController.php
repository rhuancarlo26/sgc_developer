<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecRegistro;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteController extends Controller
{
    public function __construct(private readonly RegistroService $registroService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecRegistro $registro)
    {
        $response = $this->registroService->destroy($registro);

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.registro.index', [
            'contrato' => $contrato->id,
            'servico' => $servico->id
        ])->with('message', $response['request']);
    }
}
