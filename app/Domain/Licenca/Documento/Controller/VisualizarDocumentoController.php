<?php

namespace App\Domain\Licenca\Documento\Controller;

use App\Models\Licenca;
use App\Models\LicencaDocumento;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;

class VisualizarDocumentoController extends Controller
{
    public function index(Licenca $licenca)
    {
        $arquivo = new ArquivoUtils();
        return $arquivo->visualizar($licenca->arquivo_licenca);
    }
}