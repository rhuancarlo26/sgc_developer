<?php

namespace App\Domain\Contrato\Contratada\Empreendimento\Controller;

use App\Domain\Contrato\Contratada\Empreendimento\Services\EmpreendimentoTrechoService;
use App\Domain\Contrato\Contratada\Introducao\Services\IntroducaoService;
use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoRequest;
use App\Models\Contrato;
use App\Models\ContratoIntroducao;
use App\Models\ContratoTipo;
use App\Models\SvnSegGeoV2;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class StoreContratadaEmpreendimentoTrechoController extends Controller
{
  public function __construct(private readonly EmpreendimentoTrechoService $empreendimentoTrechoService)
  {
  }

  public function index(Request $request)
  {
    $coordenada = SvnSegGeoV2::getGeoJson(
      $request->uf['uf'],
      $request->rodovia['rodovia'],
      $request->km_inicial,
      $request->km_final,
      $request->trecho_tipo ?? 'B'
    );

    $form = [
      ...$request->all(),
      'uf_id' => $request->uf['id'],
      'rodovia_id' => $request->rodovia['id'],
      'coordenada' => $coordenada
    ];

    $response = $this->empreendimentoTrechoService->salvarTrecho($form);

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}