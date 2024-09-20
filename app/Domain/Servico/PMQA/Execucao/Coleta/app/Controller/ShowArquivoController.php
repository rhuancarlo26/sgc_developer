<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller;

use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPontoColetaArquivo;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ShowArquivoController extends Controller
{

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, ServicoPmqaCampanhaPontoColetaArquivo $arquivo): BinaryFileResponse
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $arquivo->caminho_imagem);
    }
}
