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

    public function get(int $id_licenca, array $searchParams)
    {
        return $this->search(...$searchParams)
            ->where('licenca_id', $id_licenca)
            ->paginate(10)
            ->appends($searchParams);
    }

    public function create(array $post): array
    {
        $uf      = Uf::where('estado', $post['uf_inicial'])->first();
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

        return [
            'licencaSegmento' => $licencaSegmento['model'],
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

    public function delete(array $post): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $post['id']);
    }

    public function getUF(string $rodovia): array
    {
        $licencaBRService = new LicencaBRService();

        return ['ufs' => $licencaBRService->getLicencaUF($rodovia)];
    }

}
