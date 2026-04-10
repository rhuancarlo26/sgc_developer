<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Services;

use App\Models\Modulo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;
use App\Shared\Traits\Searchable;

class ConfiguracoesModulosService extends BaseModelService
{
    use ModulosHandler, Searchable;

    protected string $modelClass = Modulo::class;

    public function buscarModulos($searchParams): array
    {
        // $modulos = Modulo::paginate(10);
        $modulos = $this->searchAllColumns(...$searchParams)
            ->paginate(10)
            ->appends($searchParams);

        return [
            'modulos' => $modulos,
            'tipos' => $this->buscarParams()
        ];
    }

    public function store(array $data): array
    {
        $dataManagement = $this->dataManagement->create(entity: $this->modelClass, infos: $data);
        return $dataManagement['request'];
    }

    public function update(Modulo $modulo, array $data): array
    {
        $dataManagement = $this->dataManagement->update(entity: $this->modelClass, infos: $data, id: $modulo->id);
        return $dataManagement['request'];
    }
}
