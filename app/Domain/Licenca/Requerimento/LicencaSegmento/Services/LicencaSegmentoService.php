<?php

namespace App\Domain\Licenca\Requerimento\LicencaSegmento\Services;

use App\Domain\Licenca\AppModule\Services\LicencaBRService;
use App\Domain\Licenca\AppModule\Services\LicencaTipoService;
use App\Models\Licenca;
use App\Models\LicencaSegmento;
use App\Models\Rodovia;
use App\Models\SvnSegGeoV2;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LicencaSegmentoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = LicencaSegmento::class;

    public function create(array $post): array
    {
        $uf = Uf::where('estado', $post['uf_inicial'])->first();
        $rodovia = Rodovia::where('rodovia', $post['rodovia'])->where('uf_id', $uf->id)->first();

        $coordenada = SvnSegGeoV2::getGeoJson(
            $uf->uf,
            $rodovia->rodovia,
            $post['km_inicial'],
            $post['km_final'],
            'B'
        );

        $licencaSegmento = $this->dataManagement->create(entity: $this->modelClass, infos: [
            'coordenada' => $coordenada,
            ...$post
        ]);

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
                'tipos'             => $licencaTipoService->getLicencaTipo(),
                'licenca'           => $licenca,
                'licencaSegmento'   => $licencaSegmento['model'],
                'brs'               => $brs
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
                'tipos'             => $licencaTipoService->getLicencaTipo(),
                'licenca'           => $licenca,
                'licencaSegmento'   => $licencaSegmento['model'],
                'brs'               => $brs
            ],
            'request' => $licenca['request']
        ];
    }

    public function getUF(String $rodovia): array
    {
        $licencaBRService = new LicencaBRService();

        return [
            'ufs' => $licencaBRService->getLicencaUF($rodovia)
        ];
    }
}