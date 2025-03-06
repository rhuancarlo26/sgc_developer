<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadModeloController
{
  public function index(): BinaryFileResponse
  {
    $path = public_path('file\Servico\MonAtpFauna\Registro.xlsx');


    if (!File::exists($path)) {
      abort(404);
    }

    return Response::download($path);
  }
}