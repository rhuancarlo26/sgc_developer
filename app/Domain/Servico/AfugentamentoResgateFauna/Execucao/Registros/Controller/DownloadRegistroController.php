<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;

class DownloadRegistroController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function index(Servicos $servico)
    {
        dd($servico);
    }
}