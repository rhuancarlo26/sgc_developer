<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Services;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Imports\PMQAPontoImport;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PontoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPmqaPonto::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'pontos' => $this->searchAllColumns(...$searchParams)
                ->where('fk_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function importar(Servicos $servico, UploadedFile $arquivo): array
    {
        $pmqaPontoImport = new PMQAPontoImport();

        $pontos = Excel::toCollection($pmqaPontoImport, $arquivo)->first();

        foreach ($pontos as $row) {
            try {
                $this->dataManagement->create(entity: $this->modelClass, infos: [
                    ...$row,
                    'UF' => $row['uf'],
                    'fk_servico' => $servico->id
                ]);
            } catch (\Throwable $e) {
                Log::error("Erro ao inserir o ponto de coleta: {$row['nomepontocoleta']}, {$e->getMessage()}");
            }
        }

        return ['type' => 'success', 'content' => 'Registro cadastrado!'];
    }

    public function update(array $updateRequest): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $updateRequest, id: $updateRequest['id']);
    }

    public function deletePonto(ServicoPmqaPonto $ponto): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $ponto->id);
    }

}
