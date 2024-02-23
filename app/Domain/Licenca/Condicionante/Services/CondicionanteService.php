<?php

namespace App\Domain\Licenca\Condicionante\Services;

use App\Models\Licenca;
use App\Models\LicencaCondicionante;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use PhpParser\Node\Expr\Cast\Object_;

class CondicionanteService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = LicencaCondicionante::class;
    protected string $modelClassLicenca = Licenca::class;

    public function listarCondicionantes($licenca, $searchParams)
    {
        return $this->search(...$searchParams)
            ->where('licenca_id', $licenca['id'])
            ->paginate()
            ->appends($searchParams);
    }

    public function store($request): array
    {
        $condicionante = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        return [
            'licenca' => $request['licenca_id'],
            'request' => $condicionante['request']
        ];
    }

    public function update($request): array
    {
        $condicionante = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
        return [
            'licenca' => $request['licenca_id'],
            'request' => $condicionante['request']
        ];
    }

    public function buscarLicenca($request)
    {
        return $this->modelClassLicenca::with(['condicionantes'])->where('numero_licenca', $request['numero_licenca'])->firstOrFail();
    }

    public function storeImportacao($request)
    {
        foreach ($request['condicionantes'] as $value) {
            $value['licenca_id'] = $request['licenca']['id'];

            $condicionante = $this->dataManagement->create(entity: $this->modelClass, infos: $value);
        }

        return [
            'licenca' => $request['licenca']['id'],
            'request' => $condicionante['request']
        ];
    }
}