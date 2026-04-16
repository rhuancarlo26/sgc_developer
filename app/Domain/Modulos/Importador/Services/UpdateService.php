<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Domain\Modulos\Importador\Jobs\ProcessarPlanilhaImportadorJob;
use App\Models\ModuloImportador;
use App\Models\ModuloImportadorAnexos;
use App\Models\ModuloImportadorFotos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;

class UpdateService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = ModuloImportador::class;

    public function update(ModuloImportador $importador, array $data): void
    {
        // dd($data);
        $caminhoArquivo = null;
        $extensaoArquivo = null;

        if (!is_null($data['arquivo'])) {
            $arquivo = $data['arquivo'];
            unset($data['arquivo']);

            $nomeArquivo = $arquivo->getClientOriginalName();
            $caminhoArquivo = $arquivo->storeAs('Importador' . DIRECTORY_SEPARATOR . uniqid() .  '_' . $nomeArquivo);

            $extensaoArquivo = $arquivo->getClientOriginalExtension();

            $data['nome_arquivo'] = $nomeArquivo;
        }

        $importador->update($data);

        $job = new ProcessarPlanilhaImportadorJob(
            importadorId: $importador->id,
            caminhoArquivo: $caminhoArquivo,
            extensaoArquivo: $extensaoArquivo,
            temArquivo: !is_null($data['arquivo'])
        );

        // $job->handle();
        dispatch($job);

        $idsFotos = [];
        foreach ($data['fotos'] ?? [] as $f) {

            $dataF = [];

            if (isset($f['arquivo'])) {
                $arquivoF_ = $f['arquivo'];

                $nomeArquivoF_ = $arquivoF_->getClientOriginalName();
                $nomeCaminhoF_ = 'Modulos_Importador' . DIRECTORY_SEPARATOR . 'Fotos' . DIRECTORY_SEPARATOR . uniqid() .  '_' . $nomeArquivoF_;
                // $arquivoF_->storeAs('public' . DIRECTORY_SEPARATOR . $nomeCaminhoF_);

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
            ->delete();

        $idsAnexo = [];
        foreach ($data['anexos'] ?? [] as $a) {

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

            $idsAnexo[] = $anexo->id;
        }

        ModuloImportadorAnexos::query()
            ->where('modulo_importador_id', $importador->id)
            ->whereNotIn('id', $idsAnexo)
            ->delete();
    }
}
