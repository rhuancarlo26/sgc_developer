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

    public function get(array $searchParams, bool $arquivado = false)
    {
        return $this->searchAllColumns(...$searchParams)
            ->with(relations: ['tipo', 'requerimentos', 'documento'])
            ->where('arquivado', $arquivado)
            ->paginate(10)
            ->appends($searchParams);
    }

    public function all(array $searchParams, bool $arquivado = false)
    {
        return $this->searchAllColumns(...$searchParams)
            ->with(relations: 'tipo')
            ->where('arquivado', $arquivado)
            ->get();
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
            'rodovias' => Cache::rememberForever('rodovias', fn () => Rodovia::all()),
        ];
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function getSumArea(array $licencaIds)
    {
        return $this->model
            ->selectRaw('SUM(in_app) as in_app, SUM(out_app) as out_app')
            ->whereIn('id', $licencaIds)
            ->first();
    }

    public function getSumTotalASV(array $licencaIds)
    {
        return $this->model
            ->selectRaw('SUM(volume) as volume')
            ->whereIn('id', $licencaIds)
            ->first();
    }
}
