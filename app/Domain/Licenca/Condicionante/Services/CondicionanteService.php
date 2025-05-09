<?php

namespace App\Domain\Licenca\Condicionante\Services;

use App\Models\Licenca;
use App\Models\LicencaCondicionante;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class CondicionanteService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = LicencaCondicionante::class;
    protected string $modelClassLicenca = Licenca::class;

    public function listarCondicionantes($licenca, $searchParams): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->search(...$searchParams)
            ->where('licencas_id', $licenca['id'])
            ->paginate()
            ->appends($searchParams);
    }

    public function store($request): array
    {
        $condicionante = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        return [
            'licenca' => $request['licencas_id'],
            'request' => $condicionante['request']
        ];
    }

    public function update($request): array
    {
        $condicionante = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
        return [
            'licenca' => $request['licencas_id'],
            'request' => $condicionante['request']
        ];
    }

    public function buscarLicenca($request)
    {
        return $this->modelClassLicenca::with(['condicionantes'])->where('numero_licenca', $request['numero_licenca'])->firstOrFail();
    }

    public function storeImportacao($request): array
    {
        foreach ($request['condicionantes'] as $value) {
            $value['licencas_id'] = $request['licenca']['id'];

            $condicionante = $this->dataManagement->create(entity: $this->modelClass, infos: $value);
        }

        return [
            'request' => $condicionante['request']
        ];
    }
}