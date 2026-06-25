<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Domain\Modulos\Importador\Jobs\ProcessarPlanilhaImportadorJob;
use App\Models\ModuloImportador;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\DataManagement;
use Illuminate\Support\Arr;

class StoreService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = ModuloImportador::class;

    protected GerenciarImportadorService $gerenciarImportadorService;

    public function __construct(DataManagement $dataManagement)
    {
        $this->gerenciarImportadorService = new GerenciarImportadorService;
        return parent::__construct($dataManagement);
    }

    public function store(array $data): ModuloImportador
    {
        $arquivo = $data['arquivo'];
        $importador = null;

        try {
            $fotos = $data['fotos'] ?? [];
            $anexos = $data['anexos'] ?? [];
            $enviarAnalise = filter_var($data['enviar_analise'] ?? false, FILTER_VALIDATE_BOOLEAN);

            unset(
                $data['arquivo'],
                $data['fotos'],
                $data['anexos'],
                $data['enviar_analise'],
                $data['continuar_formulario']
            );

            $nomeArquivo = $arquivo->getClientOriginalName();

            $caminhoArquivo = $arquivo->storeAs(
                'Importador' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nomeArquivo
            );

            $data['nome_arquivo'] = $nomeArquivo;
            $data['status'] = ModuloImportador::RASCUNHO;

            $importador = ModuloImportador::create($data);

            $job = new ProcessarPlanilhaImportadorJob(
                importadorId: $importador->id,
                caminhoArquivo: $caminhoArquivo,
                extensaoArquivo: $arquivo->getClientOriginalExtension()
            );

            dispatch_sync($job);

            $this->gerenciarImportadorService->gerenciarFotos($importador, $fotos);
            $this->gerenciarImportadorService->gerenciarAnexos($importador, $anexos);

            $importador->historicos()->create([
                'usuario_id' => auth()->user()->id,
                'status' => ModuloImportador::RASCUNHO,
            ]);

            if ($enviarAnalise) {
                $dataAnalise = Arr::only($data, 'parecer_tecnico');
                (new StatusImportadorService)->enviarAnalise($importador, $dataAnalise);
            }

            return $importador;
        } catch (\Throwable $e) {
            if ($importador && $importador->exists) {
                $importador->dadosJson()->delete();
                $importador->fotos()->delete();
                $importador->anexos()->delete();
                $importador->historicos()->delete();
                $importador->delete();
            }

            throw $e;
        }
    }
}
