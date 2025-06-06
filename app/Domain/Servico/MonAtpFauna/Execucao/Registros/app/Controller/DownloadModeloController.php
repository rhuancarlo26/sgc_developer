<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadModeloController
{
    public function importarModelo(): BinaryFileResponse
    {
        $path = public_path('file/Servico/MonAtpFauna/ModeloRegistro.xlsx');

        if (!File::exists($path)) {
            abort(404, 'Arquivo modelo não encontrado.');
        }

        return Response::download($path);
    }
}
