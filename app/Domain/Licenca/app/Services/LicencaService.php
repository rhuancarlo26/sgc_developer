<?php

namespace App\Domain\Licenca\app\Services;

use App\Models\Licenca;
use App\Models\LicencaSegmento;
use App\Models\LicencaTipo;
use App\Models\Rodovia;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Cache;

class LicencaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Licenca::class;

    public function get(array $searchParams, bool $arquivado = false): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->search(...$searchParams)
            ->with(relations: ['tipo', 'requerimentos', 'documento'])
            ->where('arquivado', $arquivado)
            ->paginate(10)
            ->appends($searchParams);
    }

    public function getLicenca(int $id_licenca)
    {
        return Licenca::where('id', $id_licenca)->first();
    }

    public function create(): array
    {
        return [
            'tipos' => Cache::rememberForever('licenca_tipos', fn () => LicencaTipo::all()),
            'ufs' => Cache::rememberForever('ufs', fn () => Uf::all()),
            'rodovias' => Rodovia::groupBy('rodovia')->get()
        ];
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update(array $request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }
}
