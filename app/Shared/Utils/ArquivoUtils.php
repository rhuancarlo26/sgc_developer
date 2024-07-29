<?php

namespace App\Shared\Utils;

use App\Models\Arquivo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Response;

class ArquivoUtils
{
    public function visualizar($caminho): \Illuminate\Http\Response
    {
        return Response::make(
            content: file_get_contents(storage_path('app') . DIRECTORY_SEPARATOR . $caminho),
            headers: ["Content-type" => "application/pdf"]
        );
    }

    public function salvar(UploadedFile $arquivo, string $diretorio, ?string $prefixo = null): ?Arquivo
    {
        if (!$arquivo->isValid()) {
            return null;
        }

        $nomeArquivo = $prefixo . rand() . '.' . $arquivo->clientExtension();
        $arquivo->storeAs($diretorio, $nomeArquivo);

        /** @var Arquivo */
        return Arquivo::query()->create([
            'chave'        => md5(uniqid(rand(), true)),
            'arquivo'      => $nomeArquivo,
            'extensao'     => $arquivo->clientExtension(),
            'diretorio'    => $diretorio,
            'nome_arquivo' => $arquivo->getClientOriginalName(),
        ]);
    }
}
