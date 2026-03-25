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
        $filePath = storage_path('app/public') . DIRECTORY_SEPARATOR . $licenca->arquivo_licenca;

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->file($filePath);
    }

    public function termo(Licenca $licenca)
    {
        return response()->file(storage_path('app') . DIRECTORY_SEPARATOR . $licenca->arquivo_termo);
    }
}