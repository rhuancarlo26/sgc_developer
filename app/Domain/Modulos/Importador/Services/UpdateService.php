<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Domain\Modulos\Importador\Jobs\ProcessarPlanilhaImportadorJob;
use App\Models\ModuloImportador;
use App\Models\ModuloImportadorAnexos;
use App\Models\ModuloImportadorFotos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\DataManagement;

class UpdateService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = ModuloImportador::class;
    protected GerenciarImportadorService $gerenciarImportadorService;

    public function __construct(DataManagement $dataManagement)
    {
        $this->gerenciarImportadorService = new GerenciarImportadorService;
        return parent::__construct($dataManagement);
    }

    public function update(ModuloImportador $importador, array $data): void
    {
        $caminhoArquivo = null;
        $extensaoArquivo = null;
        $arquivo = null;

        // dd($data);

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
            temArquivo: !is_null($arquivo)
        );

        // $job->handle();
        dispatch($job);

        $this->gerenciarImportadorService->gerenciarFotos($importador, $data['fotos'] ?? []);
        $this->gerenciarImportadorService->gerenciarAnexos($importador, $data['anexos'] ?? []);
    }
}
