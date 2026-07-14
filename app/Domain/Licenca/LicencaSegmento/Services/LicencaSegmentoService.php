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
use Illuminate\Support\Facades\DB;

class LicencaSegmentoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = LicencaSegmento::class;

    public function get(object $licenca)
    {
        return $this->modelClass::with([
            'uf_inicial_rel',
            'uf_final_rel',
            'rodovias',
        ])
            ->where('licenca_id', $licenca->id)
            ->paginate(10);
    }

    public function create(array $post): array
    {
        $geometry = null;
        $coordenada = $post['coordenada'] ?? null;

        if ($coordenada) {
          $geometry = DB::raw("ST_GeomFromGeoJSON(?)", [$coordenada]);
        }

        $post['geometry'] = $geometry ?? null;

        $licencaSegmento = $this->dataManagement->create(entity: $this->modelClass, infos: $post);

        return [
            'request' => $licencaSegmento['request']
        ];
    }

    public function update(array $post): array
    {
        $geometry = null;
        $coordenada = $post['coordenada'] ?? null;

        if ($coordenada) {
          $geometry = DB::raw("ST_GeomFromGeoJSON(?)", [$coordenada]);
        }

        $post['geometry'] = $geometry ?? null;

        $licencaSegmento = $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['idlicenca_br']);

        return [
            'licencaSegmento' => $licencaSegmento['model'],
            'request'         => $licencaSegmento['request']
        ];
    }

    public function getCoordenada(array $post): string
    {
        return SvnSegGeoV2::getGeoJson(
            UF_inicial: $post['uf_inicial']['uf'],
            // UF_final: $post['uf_final']['uf'],
            rodovia: $post['rodovia'],
            km_inicial: $post['km_inicio'],
            km_final: $post['km_fim'],
            tipo_trecho: 'B'
        );
    }

    public function delete(object $post): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $post['idlicenca_br']);
    }
}
