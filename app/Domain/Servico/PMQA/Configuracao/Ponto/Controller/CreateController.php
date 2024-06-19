<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
  public function __construct(private readonly PontoService $pontoService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaPonto $ponto): Response
  {
    return Inertia::render('Servico/PMQA/Configuracao/Ponto/Form', [
      'contrato'  => $contrato,
      'servico'   => $servico,
      'ponto' => $ponto
    ]);
  }
}
