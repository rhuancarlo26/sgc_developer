<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Domain\Contrato\GestaoContrato\Requests\UpdateContratoTrechoRequest;
use App\Models\contratoTrecho;
use App\Models\SvnSegGeoV2;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateContratoTrechoController extends Controller
{
  public function updateTrecho(contratoTrecho $trecho, UpdateContratoTrechoRequest $request)
  {
    $coordenada = SvnSegGeoV2::getGeoJson(
      $request->uf['uf'],
      $request->rodovia['rodovia'],
      $request->km_inicial,
      $request->km_final,
      $request->trecho_tipo ?? 'B'
    );

    if ($trecho->update([
      ...$request->all(),
      'uf_id' => $request->uf['id'],
      'rodovia_id' => $request->rodovia['id'],
      'coordenada' => $coordenada
    ])) {
      return to_route('contratos.gestao.create', $trecho->contrato_id)->with('message', [
        'type' => 'success',
        'content' => "Trecho do contrato atualizado com sucesso"
      ]);
    }
  }
}