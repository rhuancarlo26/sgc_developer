<?php

namespace App\Domain\Licenca\Requerimento\Controller;

use App\Models\LicencaRequerimento;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;

class VisualizarRequerimentoController extends Controller
{
    public function visualizar(LicencaRequerimento $requerimento): \Illuminate\Http\Response
    {
        $arquivo = new ArquivoUtils();
        return $arquivo->visualizar($requerimento->caminho);
    }
}
