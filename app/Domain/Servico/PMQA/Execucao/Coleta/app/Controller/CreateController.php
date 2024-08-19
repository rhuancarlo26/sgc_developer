<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaCampanha;
use App\Models\ServicoPmqaCampanhaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function index(Contrato $contrato, Servicos $servico, ServicoPmqaCampanha $campanha, ServicoPmqaCampanhaPonto $ponto): Response
    {
        return Inertia::render('Servico/PMQA/Execucao/Coleta/Form', [
            'contrato' => $contrato,
            'servico'  => $servico->load(['tipo', 'pmqa_config_lista_parecer']),
            'campanha' => $campanha,
            'ponto'    => $ponto->load(['ponto', 'coleta.arquivos'])
        ]);
    }
}
