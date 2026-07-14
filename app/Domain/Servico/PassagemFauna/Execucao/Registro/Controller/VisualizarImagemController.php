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
        // As imagens de monitoramento de passagem de fauna estão vindo com public, assim fiz esse replace para retirar o public
        $caminho = str_replace('public/', '', $imagem->caminho_imagem);

        return response()->file(storage_path('app/public/' . $caminho));
    }
}
