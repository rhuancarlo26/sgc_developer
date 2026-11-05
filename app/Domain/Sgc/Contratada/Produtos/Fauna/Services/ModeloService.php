<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use RuntimeException;
use ZipArchive;

class ModeloService
{
    public function gerarZipModelos(): string
    {
        $files = ['modelo_terrestre.xlsx', 'modelo_aquatica.xlsx', 'modelo_cavernicola.xlsx'];
        foreach ($files as $file) {
            if (!is_file(resource_path('modelos/fauna/' . $file))) {
                throw new RuntimeException('Modelo de fauna indisponivel: ' . $file);
            }
        }

        $zipPath = tempnam(storage_path('app'), 'fauna_');
        if ($zipPath === false) throw new RuntimeException('Nao foi possivel criar o arquivo de modelos.');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);
            throw new RuntimeException('Nao foi possivel abrir o arquivo de modelos.');
        }
        foreach ($files as $file) {
            $zip->addFile(resource_path('modelos/fauna/' . $file), $file);
        }
        if (!$zip->close()) {
            @unlink($zipPath);
            throw new RuntimeException('Nao foi possivel gerar o arquivo de modelos.');
        }
        return $zipPath;
    }
}
