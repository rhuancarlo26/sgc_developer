<?php

namespace App\Domain\Licenca\Requerimento\Controller;

use App\Models\LicencaRequerimento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class visualizarRequerimentoController extends Controller
{
  public function visualizar(LicencaRequerimento $requerimento)
  {
    return Response::make(file_get_contents(storage_path('app') . DIRECTORY_SEPARATOR . $requerimento->caminho), 200, [
      "Content-type" => "application/pdf"
    ]);
  }
}