<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services;

use App\Models\Licenca;
use App\Models\ServicoLicenca;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VincularASVService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoLicenca::class;

    public function index(Servicos $servico): LengthAwarePaginator
    {
        return Licenca::query()
            ->with('documento')
            ->whereHas('licencaServicos', fn($q) => $q->where('servico_id', $servico->id))
            ->paginate();
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function deleteByLicencaAndServico(Licenca $licenca, Servicos $servicos): bool
    {
        $licencaServico = $this->modelClass::query()
            ->where('licenca_id', $licenca->id)
            ->where('servico_id', $servicos->id)
            ->first();

        return $this->delete(model: $licencaServico);
    }

}
