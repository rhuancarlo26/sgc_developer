<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Services\PontoService;
use App\Models\Contrato;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadModeloController extends Controller
{
  public function __construct(private readonly PontoService $pontoService)
  {
  }

  public function index(): BinaryFileResponse
  {
    $path = public_path('file\Servico\PMQA\Ponto\modelo_planilha_pontos_pmqa_teste.xlsx');


    if (!File::exists($path)) {
      abort(404);
    }

    return Response::download($path);
  }
}
