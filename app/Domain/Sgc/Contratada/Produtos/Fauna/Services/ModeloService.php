<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use ZipArchive;
use Illuminate\Support\Facades\Storage;

class ModeloService
{
    public function gerarZipModelos(): string
    {
        $zipFileName = 'modelos_fauna.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $files = [
            'modelos/fauna/modelo_terrestre.xlsx',
            'modelos/fauna/modelo_aquatica.xlsx',
            'modelos/fauna/modelo_cavernicola.xlsx',
        ];

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file)) {
                    $zip->addFile(
                        storage_path('app/public/' . $file),
                        basename($file)
                    );
                }
            }
            $zip->close();
        }

        return $zipPath;
    }
}