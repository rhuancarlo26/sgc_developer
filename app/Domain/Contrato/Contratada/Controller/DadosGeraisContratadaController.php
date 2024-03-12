<?php

namespace App\Domain\Contrato\Contratada\Controller;

use App\Models\Contrato;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DadosGeraisContratadaController extends Controller
{
  public function index(Contrato $contrato): Response
  {
    $numero_licencas = Licenca::all(['id', 'numero_licenca']);

    if ($contrato) {
      $contrato->load([
        'trechos',
        'trechos.uf',
        'trechos.rodovia',
        'introducao',
        'licenciamentos',
        'licenciamentos.requerimentos',
        'licenciamentos.tipo',
        'licenciamentos.documento',
        'licenciamento_observacoes',
      ]);
    }

    return Inertia::render('Contrato/Contratada/DadosGerais/Index', [
      'contrato' => $contrato,
      'numero_licencas' => $numero_licencas
    ]);
  }
}