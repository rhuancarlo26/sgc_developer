<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UploadService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreUploadRelatorioController extends Controller
{
  public function __construct(private readonly UploadService $uploadService)
  {
  }

  public function index(Request $request)
  {
    $response = $this->uploadService->salvarAnexo($request->all());

    return to_route('sgc.contratada.recurso.index', ['contrato' => $request->contrato_id])->with('message', $response['request']);
  }
}
