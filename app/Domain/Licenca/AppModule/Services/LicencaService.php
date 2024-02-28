<?php

namespace App\Domain\Licenca\AppModule\Services;

use App\Models\Licenca;
use App\Models\LicencaSegmento;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LicencaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Licenca::class;

    public function get(array $searchParams): array
    {
        return [
            'licencas' => $this->search(...$searchParams)
                ->with(relations: 'tipo')
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function create(array $post): array
    {
        $licencaTipoService = new LicencaTipoService();
        
        $licenca = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

        return [
            'licenca' => [
                'tipos'   => $licencaTipoService->getLicencaTipo(),
                'licenca' => $licenca['model']
            ],
            'request' => $licenca['request']
        ];
    }

    public function update(array $post): array
    {
        $licencaTipoService = new LicencaTipoService();

        $licenca = $this->dataManagement->update(entity: $this->modelClass, infos: $post);

        return [
            'licenca' => [
                'tipos'   => $licencaTipoService->getLicencaTipo(),
                'licenca' => $licenca['model']
            ],
            'request' => $licenca['request']
        ];
    }
}
