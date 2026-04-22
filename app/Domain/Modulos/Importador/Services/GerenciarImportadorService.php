<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Models\ModuloImportador;
use App\Models\ModuloImportadorAnexos;
use App\Models\ModuloImportadorFotos;
use App\Shared\Abstract\BaseModelService;
use Illuminate\Support\Facades\Storage;

class GerenciarImportadorService
{
    public function gerenciarFotos(ModuloImportador $importador, array $fotos): void
    {

        $idsFotos = [];
        foreach ($fotos ?? [] as $f) {

            $dataF = [];

            if (isset($f['arquivo'])) {
                $arquivoF_ = $f['arquivo'];

                $nomeArquivoF_ = $arquivoF_->getClientOriginalName();
                $nomeCaminhoF_ = 'Modulos_Importador' . DIRECTORY_SEPARATOR . 'Fotos' . DIRECTORY_SEPARATOR . uniqid() .  '_' . $nomeArquivoF_;
                $arquivoF_->storeAs('public' . DIRECTORY_SEPARATOR . $nomeCaminhoF_);

                $dataF['nome_arquivo'] = $nomeArquivoF_;
                $dataF['caminho_arquivo'] = $nomeCaminhoF_;
            }

            $foto = ModuloImportadorFotos::updateOrCreate(
                ['id' => $f['id'] ?? null],
                [
                    'modulo_importador_id' => $importador->id,
                    'latitude' => $f['latitude'],
                    'longitude' => $f['longitude'],
                    'descricao' => $f['descricao'],
                    ...$dataF
                ]
            );

            $idsFotos[] = $foto->id;
        }

        ModuloImportadorFotos::query()
            ->where('modulo_importador_id', $importador->id)
            ->whereNotIn('id', $idsFotos)
            ->get()
            ->each(function ($item) {
                $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $item->caminho_arquivo;
                if (Storage::exists($caminhoStorage))
                    Storage::delete($caminhoStorage);

                $item->delete();
            });
    }

    public function gerenciarAnexos(ModuloImportador $importador, array $anexos): void
    {
        $idsAnexos = [];
        foreach ($anexos ?? [] as $a) {

            $dataA = [];

            if (isset($a['arquivo'])) {
                $arquivoA_ = $a['arquivo'];

                $nomeArquivoA_ = $arquivoA_->getClientOriginalName();
                $nomeCaminhoA_ = 'Modulos_Importador' . DIRECTORY_SEPARATOR . 'Anexos' . DIRECTORY_SEPARATOR . uniqid() .  '_' . $nomeArquivoA_;
                $arquivoA_->storeAs('public' . DIRECTORY_SEPARATOR . $nomeCaminhoA_);

                $dataA['nome_arquivo'] = $nomeArquivoA_;
                $dataA['caminho_arquivo'] = $nomeCaminhoA_;
            }


            $anexo = ModuloImportadorAnexos::updateOrCreate(
                ['id' => $a['id'] ?? null],
                [
                    'modulo_importador_id' => $importador->id,
                    ...$dataA
                ]
            );

            $idsAnexos[] = $anexo->id;
        }

        ModuloImportadorAnexos::query()
            ->where('modulo_importador_id', $importador->id)
            ->whereNotIn('id', $idsAnexos)
            ->get()
            ->each(function ($item) {
                $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $item->caminho_arquivo;
                if (Storage::exists($caminhoStorage))
                    Storage::delete($caminhoStorage);

                $item->delete();
            });
    }
}
