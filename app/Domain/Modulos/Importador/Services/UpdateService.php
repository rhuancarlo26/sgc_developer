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
use Illuminate\Support\Arr;

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
        $arquivo = $data['arquivo'] ?? null;

        $fotos = $data['fotos'] ?? [];
        $anexos = $data['anexos'] ?? [];

        $enviarAnalise = filter_var($data['enviar_analise'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $updateModulo = filter_var($data['update_modulo'] ?? false, FILTER_VALIDATE_BOOLEAN);

        unset(
            $data['fotos'],
            $data['anexos'],
            $data['enviar_analise'],
            $data['update_modulo'],
            $data['arquivo']
        );

        if (!is_null($arquivo)) {
            $nomeArquivo = $arquivo->getClientOriginalName();

            $caminhoArquivo = $arquivo->storeAs(
                'Importador' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nomeArquivo
            );

            $extensaoArquivo = $arquivo->getClientOriginalExtension();

            $data['nome_arquivo'] = $nomeArquivo;
            $data['load'] = true;
        }

        if ($updateModulo) {
            $importador->dadosJson()->delete();
        }

        $importador->update($data);

        $job = new ProcessarPlanilhaImportadorJob(
            importadorId: $importador->id,
            caminhoArquivo: $caminhoArquivo,
            extensaoArquivo: $extensaoArquivo,
            temArquivo: !is_null($arquivo)
        );

        dispatch_sync($job);

        $this->gerenciarImportadorService->gerenciarFotos($importador, $fotos);

        $this->gerenciarImportadorService->gerenciarAnexos($importador, $anexos);

        if ($enviarAnalise) {
            $dataAnalise = Arr::only($data, ['parecer_tecnico']);

            (new StatusImportadorService)->enviarAnalise($importador, $dataAnalise);
        }
    }
}
