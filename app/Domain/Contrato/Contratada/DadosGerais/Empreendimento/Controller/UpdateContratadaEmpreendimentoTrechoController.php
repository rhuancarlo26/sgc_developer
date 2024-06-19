<?php

namespace App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Controller;

use App\Domain\Contrato\Contratada\DadosGerais\Empreendimento\Services\EmpreendimentoTrechoService;
use App\Models\SvnSegGeoV2;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateContratadaEmpreendimentoTrechoController extends Controller
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

    $response = $this->empreendimentoTrechoService->update($form);

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
