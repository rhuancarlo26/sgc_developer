<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Controller;

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
    return Inertia::render('Servico/PMQA/Execucao/Medir/Form', [
      'contrato'  => $contrato,
      'servico'   => $servico->load(['tipo']),
      'campanha' => $campanha,
      'ponto' => $ponto->load([
        'ponto.lista.parametros_vinculados.parametro',
        'medicao.arquivos',
        'medicao.parametros'
      ])
    ]);
  }
}