<?php

namespace App\Domain\Licenca\LicencaSegmento\Services;

use App\Domain\Licenca\app\Services\LicencaBRService;
use App\Domain\Licenca\app\Services\LicencaTipoService;
use App\Models\Licenca;
use App\Models\LicencaSegmento;
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
        $licencaSegmento = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

        $licenca = Licenca::where('id', $post['licenca_id'])->first();
        $licencaTipoService = new LicencaTipoService();
        $licencaBRService = new LicencaBRService();
        $brs = $licencaBRService->getLicencaBR();

        return [
            'licenca' => [
                'tipos' => $licencaTipoService->getLicencaTipo(),
                'licenca' => $licenca,
                'licencaSegmento' => $licencaSegmento['model'],
                'brs' => $brs
            ],
            'request' => $licenca['request']
        ];
    }

    public function delete(array $post): array
    {
        $licencaSegmento = $this->dataManagement->delete(entity: $this->modelClass, infos: $post);

        $licenca = Licenca::where('id', $post['licenca_id'])->first();
        $licencaTipoService = new LicencaTipoService();
        $licencaBRService = new LicencaBRService();
        $brs = $licencaBRService->getLicencaBR();

        return [
            'licenca' => [
                'tipos' => $licencaTipoService->getLicencaTipo(),
                'licenca' => $licenca,
                'licencaSegmento' => $licencaSegmento['model'],
                'brs' => $brs
            ],
            'request' => $licenca['request']
        ];
    }

    public function getUF(string $rodovia): array
    {
        $licencaBRService = new LicencaBRService();

        return [
            'ufs' => $licencaBRService->getLicencaUF($rodovia)
        ];
    }

}
