<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Services;

use App\Models\Modulo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\ModulosHandler;

class ConfiguracoesModulosService extends BaseModelService
{
    use ModulosHandler;

    protected string $modelClass = Modulo::class;

    public function buscarModulos(): array
    {
        $modulos = Modulo::paginate(10);
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
