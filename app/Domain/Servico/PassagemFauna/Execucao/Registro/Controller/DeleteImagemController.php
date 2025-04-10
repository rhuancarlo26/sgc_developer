<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Registro\Services\RegistroService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecRegistroImagem;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DeleteImagemController extends Controller
{
    public function __construct(private readonly RegistroService $registroService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecRegistroImagem $imagem)
    {
        $response = $this->registroService->deleteImagem($imagem);

        return to_route('contratos.contratada.servicos.passagem_fauna.execucao.registro.create', [
            'contrato' => $contrato->id,
            'servico' => $servico->id,
            'registro' => $imagem->id_registro
        ])->with('message', $response['request']);
    }
}
