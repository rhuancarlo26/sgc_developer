<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadModeloController extends Controller
{
    public function index(): BinaryFileResponse
    {
        $path = public_path('file' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PMQA' . DIRECTORY_SEPARATOR . 'Ponto' . DIRECTORY_SEPARATOR . 'modelo_planilha_pontos_pmqa_teste.xlsx');

        if (!File::exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Response::download($path);
    }
}
