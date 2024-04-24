<?php

namespace App\Domain\Licenca\LicencaSegmento\Services;

use App\Domain\Licenca\app\Services\LicencaBRService;
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

    public function get(object $licenca)
    {
        return $this->modelClass::with([
            'uf_inicial',
            'uf_final',
            'rodovia'
        ])
            ->where('licenca_id', $licenca->id)
            ->paginate(10);
    }

    public function create(array $post): array
    {
        $rodovia = Rodovia::where('rodovia', '020')->where('uf_id', $post['uf_inicial_id'])->first();

        $coordenada = SvnSegGeoV2::getGeoJson(
            $post['uf_inicial']['estado'],
            $rodovia->rodovia,
            $post['km_inicial'],
            $post['km_final'],
            'B'
        );

        $licencaSegmento = $this->dataManagement->create(entity: $this->modelClass, infos: [
            'coordenada' => $coordenada,
            ...$post
        ]);

        return [
            'request' => $licencaSegmento['request']
        ];
    }

    public function update(array $post): array
    {
        $licencaSegmento = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);

        return [
            'licencaSegmento' => $licencaSegmento['model'],
            'request'         => $licencaSegmento['request']
        ];
    }

    public function delete(object $post): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $post['id']);
    }

    public function getUF(string $rodovia): array
    {
        $licencaBRService = new LicencaBRService();

        return ['ufs' => $licencaBRService->getLicencaUF($rodovia)];
    }
}
