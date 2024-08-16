<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Services;

use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoConOcorrSupervisaoResultado::class;

    public function index(int $servico_id, array $searchParams): array
    {
        return [
            'resultados' => $this->searchAllColumns(...$searchParams)
                ->where('id_servico', $servico_id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function store(array $post): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function update(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function destroy(int $resultado_id): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $resultado_id);
    }
}
