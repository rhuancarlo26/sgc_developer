<?php

namespace App\Domain\Fiscal\app\Controllers;

use App\Domain\Fiscal\app\services\FiscalRncService;
use App\Models\Contrato;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParecerFiscalController extends Controller
{
  public function __construct(private readonly FiscalRncService $fiscalRncService) {}

  public function index(Contrato $contrato, Request $request)
  {
    $post = $request->all();

    if ($post['aprovado_rnc'] === 'Aprovado')
      $id_ocorrencia_levantamento = 6;
    else if ($post['aprovado_rnc'] === 'Em análise') {
      $id_ocorrencia_levantamento = 6;
      $post['envio_fiscal'] = 'Sim';
    } else {
      $post['envio_fiscal'] = 'Não';
      $id_ocorrencia_levantamento = 7;
    }

    $response = $this->fiscalRncService->emitirParecer($id_ocorrencia_levantamento, $post);

    return to_route('fiscal.rnc.index', ['contrato' => $contrato->id])->with('message', $response['request']);
  }
}
