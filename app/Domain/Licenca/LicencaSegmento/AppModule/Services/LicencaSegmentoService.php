<?php

namespace App\Domain\Licenca\LicencaSegmento\AppModule\Services;

use App\Domain\Licenca\AppModule\Services\LicencaBRService;

use App\Models\Licenca;
use App\Models\LicencaSegmento;
use App\Domain\Licenca\AppModule\Services\LicencaTipoService;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LicencaSegmentoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = LicencaSegmento::class;

    public function create(array $post): array
    {
        $licencaSegmento = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

        $licenca = Licenca::where('id', $post['licenca_id'])->first();
        $licencaTipoService = new LicencaTipoService();
        $licencaBRService = new LicencaBRService();
        $brs = $licencaBRService->getLicencaBR();

        return [
            'licenca' => [
                'tipos'             => $licencaTipoService->getLicencaTipo(),
                'licenca'           => $licenca,
                'licencaSegmento'   => $licencaSegmento['model'],
                'brs'               => $brs
            ],
            'request' => $licenca['request']
        ];
    }

    public function update(array $post): array
    {
        $licencaSegmento = $this->dataManagement->update(entity: $this->modelClass, infos: $post);

        $licenca = Licenca::where('id', $post['licenca_id'])->first();
        $licencaTipoService = new LicencaTipoService();
        $licencaBRService = new LicencaBRService();
        $brs = $licencaBRService->getLicencaBR();

        return [
            'licenca' => [
                'tipos'             => $licencaTipoService->getLicencaTipo(),
                'licenca'           => $licenca,
                'licencaSegmento'   => $licencaSegmento['model'],
                'brs'               => $brs
            ],
            'request' => $licenca['request']
        ];
    }

    public function delete (Array $post): array
    {   
        $licencaSegmento = $this->dataManagement->delete(entity: $this->modelClass, infos: $post);

        $licenca = Licenca::where('id', $post['licenca_id'])->first();
        $licencaTipoService = new LicencaTipoService();
        $licencaBRService = new LicencaBRService();
        $brs = $licencaBRService->getLicencaBR();

        return [
            'licenca' => [
                'tipos'             => $licencaTipoService->getLicencaTipo(),
                'licenca'           => $licenca,
                'licencaSegmento'   => $licencaSegmento['model'],
                'brs'               => $brs
            ],
            'request' => $licenca['request']
        ];
    }

    public function getUF (String $rodovia): array 
    { 
        $licencaBRService = new LicencaBRService();

        return [
            'ufs' => $licencaBRService->getLicencaUF($rodovia)
        ];
    }

}
