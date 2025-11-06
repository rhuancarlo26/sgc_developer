<?php

namespace App\Shared\Utils;

use App\Models\Arquivo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ArquivoUtils
{
    public function visualizar($caminho): \Illuminate\Http\Response
    {
        return Response::make(
            content: file_get_contents(storage_path('app/public') . DIRECTORY_SEPARATOR . $caminho),
            headers: ["Content-type" => "application/pdf"]
        );
    }

    public function salvar(UploadedFile $arquivo, string $diretorio, ?string $prefixo = null, $createModel = true): ?Arquivo
    {
        if (!$arquivo->isValid()) {
            return null;
        }

        $diretorio = str_starts_with($diretorio, 'public/') ? substr($diretorio, 7) : $diretorio;


        $nomeArquivo = ($prefixo ?? '') . uniqid() . '.' . $arquivo->getClientOriginalExtension();

        $caminhoWeb = '/' . trim($diretorio, '/') . '/' . $nomeArquivo;

        $caminhoDestino = public_path($diretorio);
        if (!is_dir($caminhoDestino)) {
            mkdir($caminhoDestino, 0775, true);
        }

        $arquivo->move($caminhoDestino, $nomeArquivo);

        $model = new Arquivo([
            'chave'         => md5(uniqid(rand(), true)),
            'arquivo'       => $nomeArquivo,
            'extensao'      => $arquivo->getClientOriginalExtension(),
            'diretorio'     => $diretorio,
            'nome_arquivo'  => $arquivo->getClientOriginalName(),
            'caminho'       => $caminhoWeb,
        ]);

        if ($createModel) {
            $model->save();
        }

        return $model;
    }

    public function delete(Arquivo $arquivo): bool
    {
        if (Storage::delete(storage_path($arquivo->diretorio . $arquivo->arquivo))) {
            return $arquivo->delete();
        }
        return false;
    }

    public function handleFotos(array $fotos, string $diretorio, string $prefixo, ?callable $afterSave = null): array
    {
        if (!count($fotos)) return [];

        $fotosId = array_filter(array_map(function ($foto) use ($diretorio, $prefixo) {
            if (!$foto instanceof UploadedFile) {
                logger()->error('Arquivo inválido recebido em handleFotos', ['tipo' => gettype($foto)]);
                return null;
            }

            $arquivo = $this->salvar($foto, $diretorio, $prefixo);
            return $arquivo?->id;
        }, $fotos));

        if ($afterSave !== null) {
            $afterSave($fotosId);
        }

        return $fotosId;
    }
}
