<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Controller;

use App\Models\Contrato;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{

    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaPonto $ponto): Response
    {
        return Inertia::render('Servico/PMQA/Configuracao/Ponto/Form', [
            'contrato' => $contrato,
            'servico' => $servico->load(['tipo']),
            'ponto' => $ponto
        ]);
    }
}
