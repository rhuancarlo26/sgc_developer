<?php

namespace App\Domain\Contrato\GestaoContrato\Services;

use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Models\Rodovia;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Cache;

class ListagemContratoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;

    public function ListagemContratos($tipo, $searchParams)
    {
        return [
            'contratos' => $this->searchAllColumns(...$searchParams)
                ->with(['aditivos'])
                ->where('tipo_contrato', $tipo->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function create($contrato)
    {
        $ufs = Cache::rememberForever('ufs', fn () => Uf::all());
        $rodovias = Cache::rememberForever('rodovias', fn () => Rodovia::all());
        $tipos = Cache::rememberForever('contrato_tipos', fn () => ContratoTipo::all());

        if ($contrato) {
            $contrato->load([
                'tipo',
                'aditivos',
                'trechos',
                'trechos.uf',
                'trechos.rodovia'
            ]);
        }

        return [
            'ufs' => $ufs,
            'rodovias' => $rodovias,
            'tipos' => $tipos,
            'contrato' => $contrato
        ];
    }

    public function store($request)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update($request)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }
}
