<?php

namespace App\Domain\Contrato\Contratada\Controller;

use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DadosGeraisContratadaController extends Controller
{
  public function index(Contrato $contrato): Response
  {
    if ($contrato) {
      $contrato->load([
        'trechos',
        'trechos.uf',
        'trechos.rodovia'
      ]);
    }
    return Inertia::render('Contrato/Contratada/DadosGerais/Index', [
      'contrato' => $contrato
    ]);
  }
}