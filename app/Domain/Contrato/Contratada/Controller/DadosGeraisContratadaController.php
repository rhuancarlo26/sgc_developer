<?php

namespace App\Domain\Contrato\Contratada\Controller;

use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Rodovia;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class DadosGeraisContratadaController extends Controller
{
  public function index(Contrato $contrato): Response
  {
    $ufs = Cache::rememberForever('ufs', fn () => Uf::all());
    $rodovias = Cache::rememberForever('rodovias', fn () => Rodovia::all());
    $numero_licencas = Licenca::all(['id', 'numero_licenca']);

    if ($contrato) {
      $contrato->load([
        'anexos',
        'trechos',
        'trechos.uf',
        'trechos.rodovia',
        'introducao',
        'historico',
        'licenciamentos',
        'licenciamentos.requerimentos',
        'licenciamentos.tipo',
        'licenciamentos.documento',
        'licenciamento_observacoes',
        'empreendimento_trechos',
        'empreendimento_trechos.uf',
        'empreendimento_trechos.rodovia'
      ]);
    }

    return Inertia::render('Contrato/Contratada/DadosGerais/Index', [
      'contrato' => $contrato,
      'numero_licencas' => $numero_licencas,
      'ufs' => $ufs,
      'rodovias' => $rodovias
    ]);
  }
}