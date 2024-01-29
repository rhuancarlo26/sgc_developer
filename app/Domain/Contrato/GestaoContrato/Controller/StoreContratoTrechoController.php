<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\StoreContratoTrechoRequest;
use App\Models\contratoTrecho;
use App\Models\SvnSegGeoV2;
use App\Shared\Http\Controllers\Controller;

class StoreContratoTrechoController extends Controller
{
  public function storeTrecho(StoreContratoTrechoRequest $request)
  {
    $coordenada = SvnSegGeoV2::getGeoJson(
      $request->uf['uf'],
      $request->rodovia['rodovia'],
      $request->km_inicial,
      $request->km_final,
      $request->trecho_tipo ?? 'B'
    );

    if (contratoTrecho::create([
      ...$request->all(),
      'uf_id' => $request->uf['id'],
      'rodovia_id' => $request->rodovia['id'],
      'coordenada' => $coordenada
    ])) {
      return to_route('contratos.gestao.create', $request->contrato_id)->with('message', [
        'type' => 'success',
        'content' => "Trecho do contrato criado com sucesso"
      ]);
    }
  }
}