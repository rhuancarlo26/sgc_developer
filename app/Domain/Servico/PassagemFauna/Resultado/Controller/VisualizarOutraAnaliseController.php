<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Controller;

use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaResultado;
use App\Models\ServicoPassagemFaunaResultadoOutrasAnalise;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class VisualizarOutraAnaliseController extends Controller
{
    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaResultado $resultado, ServicoPassagemFaunaResultadoOutrasAnalise $outra_analise)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $outra_analise->caminho_arquivo);
    }
}
