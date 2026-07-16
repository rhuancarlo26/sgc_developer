<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Models\ModuloImportador;
use Illuminate\Support\Facades\Storage;

class DestroyService
{
    public function destroy(ModuloImportador $importador): array
    {
        $importador->fotos->each(function ($item) {
            $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $item->caminho_arquivo;
            if (Storage::exists($caminhoStorage))
                Storage::delete($caminhoStorage);
        });

        $importador->anexos->each(function ($item) {
            $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $item->caminho_arquivo;
            if (Storage::exists($caminhoStorage))
                Storage::delete($caminhoStorage);
        });

        $importador->delete();

        return [
            'type'    => 'success',
            'content' => 'Importação excluída com sucesso!'
        ];
    }
}
