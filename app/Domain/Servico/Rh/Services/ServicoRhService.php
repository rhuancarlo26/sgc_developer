<?php

namespace App\Domain\Servico\Rh\Services;

use App\Models\ServicoRh;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\DB;

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

    public static function getRhServico($servicoId)
    {
        return ServicoRh::select([
            'servico_rh.id',
            DB::raw("(select GROUP_CONCAT(foto.id SEPARATOR ',') from rh_arquivo as foto where rh.id = foto.cod_rh) as fotos"),
            'rh.*'
        ])
            ->join('rh', 'rh.id', '=', 'servico_rh.id_rh')
            ->where('id_servico', $servicoId)
            ->get();
    }
}
