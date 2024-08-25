<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Services;

use App\Models\Servicos;
use App\Models\SupressaoRelatorio;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RelatorioService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = SupressaoRelatorio::class;

    public function index(Servicos $servico, array $searchParams): LengthAwarePaginator
    {
        return $this->searchAllColumns(...$searchParams)
            ->where('fk_servico', $servico->id)
            ->paginate()
            ->appends($searchParams);
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'fk_status' => 1,
        ]);
    }

}
