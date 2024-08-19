<?php

namespace App\Domain\Servico\Rh\Services;

use App\Models\ServicoRh;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ServicoRhService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoRh::class;

    public function storeServicoRh($request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);
        return ['request' => $response['request']];
    }

    public function deleteRh($rh, $request): array
    {
        try {
            $this->modelClass::Where('id_rh', $rh->id)->where('id_servico', $request['servico_id'])->firstOrFail()->delete();

            $response = [
                'type' => 'success',
                'content' => 'Recurso deletado com sucesso!'
            ];
        } catch (\Exception $e) {
            $response = [
                'type' => 'error',
                'content' => $e->getMessage()
            ];
        }

        return ['request' => $response];
    }
}
