<?php

namespace App\Domain\Servico\PMQA\Execucao\Controller;

use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{

  public function index(Contrato $contrato, Servicos $servico): Response
  {

    return Inertia::render('Servico/PMQA/Execucao/Index', [
      'contrato' => $contrato,
      'servico' => $servico
    ]);
  }
}