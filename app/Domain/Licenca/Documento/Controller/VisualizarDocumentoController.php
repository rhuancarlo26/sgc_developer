<?php

namespace App\Domain\Licenca\Documento\Controller;

use App\Models\LicencaDocumento;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;

class VisualizarDocumentoController extends Controller
{
    public function index(LicencaDocumento $documento)
    {
        $arquivo = new ArquivoUtils();
        return $arquivo->visualizar($documento->caminho);
    }
}
