<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Services\VincularABIOService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaConfigAbioRet;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class VisualizarAbioRetController extends Controller
{
    public function __construct(private readonly VincularABIOService $ABIOService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaConfigAbioRet $abio_ret)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $abio_ret->caminho_ret);
    }
}
