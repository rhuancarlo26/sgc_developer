<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;

class RegistrosController extends Controller
{
    public function __construct()
    {
    }

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        return Inertia::render('Servico/AfugentamentoResgateFauna/Execucao/Registros', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            // ...$response
        ]);
    }
}