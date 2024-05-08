<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UploadService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateUploadRelatorioController extends Controller
{
  public function __construct(private readonly UploadService $anexoService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->anexoService->update($request->all());

    return to_route('contratos.contratada.dados_gerais.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
