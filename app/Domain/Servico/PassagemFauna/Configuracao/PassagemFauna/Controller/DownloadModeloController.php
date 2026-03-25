<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Controller;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadModeloController
{
    public function index(): BinaryFileResponse
    {
        $path = public_path('file/Servico/PassagemFauna/Configuracao/PassagemFauna/modelo_passagem_de_fauna.xlsx');


        if (!File::exists($path)) {
            abort(404);
        }

        return Response::download($path);
    }
}
