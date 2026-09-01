<?php

namespace App\Domain\Modulos\Importador\Controllers;

use App\Models\ModuloImportadorAnexos;
use App\Models\ModuloImportadorFotos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ArquivoImportadorController extends Controller
{
    public function foto(ModuloImportadorFotos $foto): BinaryFileResponse
    {
        return response()->file($this->caminhoAbsoluto($foto->caminho_arquivo));
    }

    public function anexo(ModuloImportadorAnexos $anexo): BinaryFileResponse
    {
        return response()->download(
            $this->caminhoAbsoluto($anexo->caminho_arquivo),
            $anexo->nome_arquivo ?: basename($anexo->caminho_arquivo)
        );
    }

    /**
     * Resolve o caminho absoluto do arquivo em storage/app/public, tolerando
     * barras invertidas e o prefixo "public/" que possa ter sido gravado.
     */
    private function caminhoAbsoluto(?string $caminho): string
    {
        $caminho = str_replace('\\', '/', (string) $caminho);
        $caminho = preg_replace('#^/?public/#', '', $caminho);
        $caminho = ltrim($caminho, '/');

        $caminhoAbsoluto = Storage::disk('public')->path($caminho);

        abort_unless($caminho !== '' && is_file($caminhoAbsoluto), 404);

        return $caminhoAbsoluto;
    }
}
