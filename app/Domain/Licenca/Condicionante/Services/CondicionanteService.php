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
        return Licenca::with(['condicionantes'])->where('numero_licenca', $request['numero_licenca'])->firstOrFail();
    }

    public function storeImportacao($request)
    {
        try {
            foreach ($request['condicionantes'] as $value) {
                $value['licenca_id'] = $request['licenca']['id'];

                $this->modelClass::create($value);
            }
            return ['type' => 'success', 'content' => 'Condicionante alterado com sucesso!'];
        } catch (\Exception $th) {
            return ['type' => 'error', 'content' => $th->getMessage()];
        }
    }
}
