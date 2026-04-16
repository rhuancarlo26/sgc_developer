<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Domain\Modulos\Importador\Jobs\ProcessarPlanilhaImportadorJob;
use App\Models\Contrato;
use App\Models\Modulo;
use App\Models\ModuloImportador;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;
use Illuminate\Database\Eloquent\Collection;

class StoreService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = ModuloImportador::class;

    public function store(array $data): void
    {
        $arquivo = $data['arquivo'];
        unset($data['arquivo']);

        $nomeArquivo = $arquivo->getClientOriginalName();
        $caminhoArquivo = $arquivo->storeAs('Importador' . DIRECTORY_SEPARATOR . uniqid() .  '_' . $nomeArquivo);

        $data['nome_arquivo'] = $nomeArquivo;
        $importador = ModuloImportador::create($data);

        $job = new ProcessarPlanilhaImportadorJob(
            importadorId: $importador->id,
            caminhoArquivo: $caminhoArquivo,
            extensaoArquivo: $arquivo->getClientOriginalExtension()
        );

        // $job->handle();
        dispatch($job);
    }
}
