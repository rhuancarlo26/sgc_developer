<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Controller;

use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecRegistroImagem;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class VisualizarImagemController extends Controller
{
    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecRegistroImagem $imagem)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $imagem->caminho_imagem);
    }
}
